<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class RateControllerTest extends WebTestCase
{
    public function testBadParams(): void
    {
        $client = RateControllerTest::createClient();
        $client->request('GET', '/api/rates?pair=WRONG&period=day&date=2025-09-10');
        $this->assertResponseStatusCodeSame(400);
    }
}
