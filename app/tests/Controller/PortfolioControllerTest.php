<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class PortfolioControllerTest extends WebTestCase
{
    public function testHistory(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/portfolio/history');

        self::assertResponseIsSuccessful();
    }
}
