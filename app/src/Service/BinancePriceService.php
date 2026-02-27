<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class BinancePriceService
{
    private HttpClientInterface $httpClient;
    private const BASE_URL = 'https://api.binance.com';

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Returns the price of given currency.
     */
    public function getExchangeRate(string $code): float
    {
        // Special-case USDT itself
        if (strtoupper($code) === 'USDT') {
            return 1.0;
        }

        $symbol = strtoupper($code).'USDT';

        $response = $this->httpClient->request(
            'GET',
            self::BASE_URL.'/api/v3/avgPrice',
            [
                'query' => [
                    'symbol' => $symbol,
                ],
            ]
        );

        $data = $response->toArray(false);

        if (!isset($data['price'])) {
            throw new \RuntimeException(sprintf('Unexpected response from Binance for symbol "%s".', $symbol));
        }

        return (float) $data['price'];
    }
}
