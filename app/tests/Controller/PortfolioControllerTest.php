<?php
declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class PortfolioControllerTest extends WebTestCase
{
    /**
     * @return void
     * @throws \JsonException
     */
    public function testHistoryReturnsValidJson(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/portfolio/history');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');

        $data = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertIsArray($data);

        if ($data !== []) {
            self::assertArrayHasKey('time', $data[0]);
            self::assertArrayHasKey('amount_usdt', $data[0]);
        }
    }
}
