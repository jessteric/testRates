<?php
namespace App\Domain\Rates;

enum CryptoPairs: string
{
    case EUR_BTC = 'EUR/BTC';
    case EUR_ETH = 'EUR/ETH';
    case EUR_LTC = 'EUR/LTC';

    public function toBinanceSymbol(): string
    {
        return match ($this) {
            self::EUR_BTC => 'BTCEUR',
            self::EUR_ETH => 'ETHEUR',
            self::EUR_LTC => 'LTCEUR',
        };
    }

    /** @return self[] */
    public static function all(): array
    {
        return [self::EUR_BTC, self::EUR_ETH, self::EUR_LTC];
    }

    public static function tryFromQuery(?string $value): ?self
    {
        return match ($value) {
            'EUR/BTC' => self::EUR_BTC,
            'EUR/ETH' => self::EUR_ETH,
            'EUR/LTC' => self::EUR_LTC,
            default => null,
        };
    }
}
