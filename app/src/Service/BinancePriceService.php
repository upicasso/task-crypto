<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\PortfolioValue;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class BinancePriceService
{
    /**
     * Const base url
     */
    private const BASE_URL = 'https://api.binance.com';

    /**
     * @param HttpClientInterface $httpClient
     */
    public function __construct(
        private readonly HttpClientInterface $httpClient
    ) {
    }

    /**
     * @param string $code
     * @return float

     * @throws \HttpException
     */
    public function getExchangeRate(string $code): float
    {
        if (strtoupper($code) === PortfolioValue::PORTFOLIO_VALUE_CURRENCY) {
            return 1.0;
        }

        $symbol = strtoupper($code). PortfolioValue::PORTFOLIO_VALUE_CURRENCY;

        $response = $this->httpClient->request(
            'GET',
            self::BASE_URL.'/api/v3/avgPrice',
            [
                'query' => [
                    'symbol' => $symbol,
                ],
            ]
        );

        if ($response->getStatusCode() !== 200) {
            throw new \HttpException("Server response status code: " . $response->getStatusCode());
        }

        $data = $response->toArray(false);

        if (!isset($data['price'])) {
            throw new \RuntimeException(sprintf('Unexpected response from Binance for symbol "%s".', $symbol));
        }

        return (float) $data['price'];
    }
}
