<?php

namespace App\Controller;

use App\Domain\Rates\CryptoPairs;
use App\Services\Queries\Rates;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

readonly class RateController
{
    public function __construct(private Rates $query) {}

    #[Route('/api/rates', name: 'api_rates', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $pair = CryptoPairs::tryFromQuery($request->query->get('pair'));
        $period = (string) $request->query->get('period', 'last-24h');
        if (!$pair) {
            return new JsonResponse(['error' => 'Invalid pair. Use EUR/BTC, EUR/ETH or EUR/LTC'], 400);
        }
        try {
            $payload = $this->query->series($pair, $period, [
                'date' => $request->query->get('date'),
                'month' => $request->query->get('month'),
                'from' => $request->query->get('from'),
                'to' => $request->query->get('to'),
            ]);
            return new JsonResponse($payload);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/api/rates/last-24h', name: 'api_rates_last24h', methods: ['GET'])]
    public function last24h(Request $request): JsonResponse
    {
        $q = $request->query->all();
        $q['period'] = 'last-24h';
        $request->query->replace($q);
        return $this->index($request);
    }

    #[Route('/api/rates/day', name: 'api_rates_day', methods: ['GET'])]
    public function day(Request $request): JsonResponse
    {
        $q = $request->query->all();
        $q['period'] = 'day';
        $request->query->replace($q);
        return $this->index($request);
    }
}