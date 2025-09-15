<?php

namespace App\Services\Providers;
use App\Services\Contracts\PriceProviderInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Domain\Rates\CryptoPairs;

final class BinanceProvider implements PriceProviderInterface
{
    private const URL = 'https://api.binance.com/api/v3/ticker/price';

    public function __construct(
        private readonly HttpClientInterface $http,
        private readonly LoggerInterface $logger,
    ) {}

    public function fetchPrice(CryptoPairs $pair): string
    {
        $symbol = $pair->toBinanceSymbol();
        try {
            $res = $this->http->request('GET', self::URL, [
                'query' => ['symbol' => $symbol],
                'timeout' => 5.0,
            ]);
            $payload = $res->toArray(false);
            if (!isset($payload['price']) || !is_string($payload['price'])) {
                throw new \RuntimeException('Unexpected Binance response for ' . $symbol);
            }
            return $payload['price'];
        } catch (TransportExceptionInterface $e) {
            $this->logger->error('Binance transport error', [
                'pair' => $pair->value,
                'message' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}