<?php

namespace App\Services\Queries;

use App\Repository\RateRepository;
use App\Domain\Rates\CryptoPairs;

readonly class Rates
{
    public function __construct(private RateRepository $rates) {}

    public function series(CryptoPairs $pair, string $period, array $params = []): array
    {
        [$from, $to] = $this->resolveRange($period, $params);
        $rows = $this->rates->findRange($pair, $from, $to);
        $points = array_map(static fn($r) => [
            'ts' => $r->getCollectedAt()->format(DATE_ATOM),
            'price' => $r->getPrice(),
        ], $rows);

        return [
            'pair' => $pair->value,
            'granularity' => '5m',
            'range' => [
                'from' => $from->format(DATE_ATOM),
                'to' => $to->format(DATE_ATOM),
            ],
            'points' => $points,
        ];
    }

    public function resolveRange(string $period, array $params = []): array
    {
        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $period = strtolower($period);

        return match ($period) {
            'last-24h' => [$now->sub(new \DateInterval('P1D')), $now],
            'day' => $this->rangeForDay($params['date'] ?? null),
            'month' => $this->rangeForMonth($params['month'] ?? null),
            'range' => $this->rangeForFromTo($params['from'] ?? null, $params['to'] ?? null, $now),
            default => throw new \InvalidArgumentException('Unknown period: '.$period),
        };
    }

    private function rangeForDay(?string $date): array
    {
        if (!$date || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            throw new \InvalidArgumentException('date must be YYYY-MM-DD');
        }
        $d = \DateTimeImmutable::createFromFormat('Y-m-d', $date, new \DateTimeZone('UTC'));
        if (!$d) { throw new \InvalidArgumentException('Invalid date'); }
        return [$d->setTime(0,0,0), $d->setTime(23,59,59)];
    }

    private function rangeForMonth(?string $month): array
    {
        if (!$month || !preg_match('/^\d{4}-\d{2}$/', $month)) {
            throw new \InvalidArgumentException('month must be YYYY-MM');
        }
        $tz = new \DateTimeZone('UTC');

        $from = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $month . '-01 00:00:00', $tz);
        if (!$from) {
            throw new \InvalidArgumentException('Invalid month');
        }

        $to = $from->modify('last day of this month')->setTime(23, 59, 59);
        return [$from, $to];
    }

    private function rangeForFromTo(?string $from, ?string $to, \DateTimeImmutable $fallbackTo): array
    {
        if (!$from && !$to) {
            throw new \InvalidArgumentException('range requires from or to in ISO 8601');
        }
        $tz = new \DateTimeZone('UTC');
        $fromDt = $from ? new \DateTimeImmutable($from, $tz) : $fallbackTo->sub(new \DateInterval('P1D'));
        $toDt = $to ? new \DateTimeImmutable($to, $tz) : $fallbackTo;
        if ($fromDt > $toDt) { throw new \InvalidArgumentException('from must be <= to'); }
        return [$fromDt, $toDt];
    }
}