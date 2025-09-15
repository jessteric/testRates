<?php

namespace App\Services\Contracts;

use App\Domain\Rates\CryptoPairs;

interface PriceProviderInterface
{
    public function fetchPrice(CryptoPairs $pair): string;
}