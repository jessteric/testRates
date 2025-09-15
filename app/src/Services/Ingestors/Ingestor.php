<?php

namespace App\Services\Ingestors;

use App\Domain\Rates\CryptoPairs;
use App\Services\Contracts\PriceProviderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

readonly class Ingestor
{
    public function __construct(
        private EntityManagerInterface $em,
        private PriceProviderInterface $provider,
        private LoggerInterface        $logger,
    ) {}

    public function ingestAll(): void
    {
        foreach (\CryptoPairs::all() as $pair) {
            $this->ingestOne($pair);
        }
    }

    public function ingestOne(CryptoPairs $pair): void
    {
        $price = $this->provider->fetchPrice($pair);
        $bucket = $this->floorToFiveMinutes(new \DateTimeImmutable('now', new \DateTimeZone('UTC')));

        $sql = 'INSERT INTO rate (pair, price, collected_at)
                VALUES (:pair, :price, :collected_at)
                ON CONFLICT (pair, collected_at) DO NOTHING';

        $this->em->getConnection()->executeStatement($sql, [
            'pair' => $pair->value,
            'price' => $price,
            'collected_at' => $bucket->format('Y-m-d H:i:00'),
        ]);

        $this->logger->info('Rate ingested', [
            'pair' => $pair->value,
            'bucket' => $bucket->format(DATE_ATOM),
        ]);
    }

    private function floorToFiveMinutes(\DateTimeImmutable $dt): \DateTimeImmutable
    {
        $m = (int) $dt->format('i');
        $floored = $m - ($m % 5);
        return $dt->setTime((int)$dt->format('H'), $floored, 0);
    }
}