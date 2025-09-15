<?php

namespace App\Tests;

use App\Repository\RateRepository;
use App\Services\Queries\Rates;
use PHPUnit\Framework\TestCase;

final class RatesQueryTest extends TestCase
{
    public function testResolveRangeDay(): void
    {
        $stub = $this->createMock(RateRepository::class);
        $query = new Rates($stub);
        [$from, $to] = $query->resolveRange('day', ['date' => '2025-09-10']);
        $this->assertSame('2025-09-10T00:00:00+00:00', $from->format(DATE_ATOM));
        $this->assertSame('2025-09-10T23:59:59+00:00', $to->format(DATE_ATOM));
    }

    public function testResolveRangeMonth(): void
    {
        $stub = $this->createMock(RateRepository::class);
        $query = new Rates($stub);
        [$from, $to] = $query->resolveRange('month', ['month' => '2025-02']);
        $this->assertSame('2025-02-01T00:00:00+00:00', $from->format(DATE_ATOM));
        $this->assertSame('2025-02-28T23:59:59+00:00', $to->format(DATE_ATOM));
    }

    public function testResolveRangeBadPeriod(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $stub = $this->createMock(RateRepository::class);
        (new Rates($stub))->resolveRange('weird', []);
    }
}
