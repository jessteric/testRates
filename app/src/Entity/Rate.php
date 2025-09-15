<?php

namespace App\Entity;

use App\Repository\RateRepository;
use App\Domain\Rates\CryptoPairs;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: RateRepository::class)]
#[ORM\Table(name: 'rate')]
#[ORM\UniqueConstraint(name: 'uniq_pair_bucket', columns: ['pair', 'collected_at'])]
class Rate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 16, enumType: CryptoPairs::class)]
    private CryptoPairs $pair;

    #[ORM\Column(type: 'decimal', precision: 18, scale: 8)]
    private string $price;

    #[ORM\Column(name: 'collected_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $collectedAt;

    public function __construct(CryptoPairs $pair, string $price, \DateTimeImmutable $collectedAt)
    {
        $this->pair = $pair;
        $this->price = $price;
        $this->collectedAt = $collectedAt;
    }


    public function getPair(): CryptoPairs
    {
        return $this->pair;
    }

    public function getPrice(): string
    {
        return $this->price;
    }

    public function getCollectedAt(): \DateTimeImmutable
    {
        return $this->collectedAt;
    }
}