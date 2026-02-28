<?php

namespace App\Service;

use App\Entity\PortfolioValue;
use App\Repository\InvestmentRepository;
use App\Repository\PortfolioValueRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Money\Converter;
use Money\Currencies\BitcoinCurrencies;
use Money\Currencies\CurrencyList;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Exchange\FixedExchange;
use Money\Money;

/**
 * Convert currencies, creates portfolio value entity, prepare data for the plot
 */
class PortfolioValuationService
{
    /**
     * @param EntityManagerInterface $entityManager
     * @param BinancePriceService $binancePriceService
     * @param InvestmentRepository $investmentRepository
     * @param PortfolioValueRepository $portfolioValueRepository
     */
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly BinancePriceService $binancePriceService,
        private readonly InvestmentRepository $investmentRepository,
        private readonly PortfolioValueRepository$portfolioValueRepository,
    ) {
    }

    /**
     * @return PortfolioValue
     */
    public function createNewPortfolioValuation(): PortfolioValue
    {
        $portfolioValue = new PortfolioValue();
        $portfolioValue->setAmountUsdt($this->calculatePortfolioValue());
        $portfolioValue->setCalculatedAt(new \DateTimeImmutable());

        try {
            $this->entityManager->persist($portfolioValue);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            throw new \RuntimeException("Error while saving PortfolioValue entity. Error: " . $e->getMessage());
        }

        return $portfolioValue;
    }

    /**
     * @return Money
     */
    private function calculatePortfolioValue(): Money
    {
        $investments = $this->investmentRepository->findAll();
        $portfolioValue = new Money('0', new Currency("USDT"));

        foreach ($investments as $investment) {
            if ($investment->getName() === "USDT") {
                $portfolioValue = $portfolioValue->add($investment->getValue());
            } else {
                $convertedInvestment = $this->convertToUsdt($investment->getValue());
                $portfolioValue = $portfolioValue->add($convertedInvestment);
            }
        }

        return $portfolioValue;
    }

    /**
     * @param Money $money
     * @return Money
     */
    public function convertToUsdt(Money $money): Money
    {
        return $this->getConverter($money)->convert($money, new Currency('USDT'));
    }

    /**
     * @return array
     */
    public function getPortfolioHistory(): array
    {
        $values = $this->portfolioValueRepository->getPortfolioValues();

        return $this->mapPortfolioValuesToArray($values);
    }

    /**
     * @param \DateTimeImmutable $fromParam
     * @param \DateTimeImmutable $toParam
     * @return array
     */
    public function getPortfolioHistoryByDateRange(\DateTimeImmutable $fromParam, \DateTimeImmutable $toParam): array
    {
        $values = $this->portfolioValueRepository->getPortfolioValuesByRange($fromParam, $toParam);

        return $this->mapPortfolioValuesToArray($values);
    }

    /**
     * @param int $hours
     * @return array
     * @throws \DateInvalidOperationException
     * @throws \DateMalformedIntervalStringException
     * @throws \DateMalformedStringException
     */
    public function getPortfolioHistoryByHours(int $hours): array
    {
        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $from = $now->sub(new \DateInterval('PT'.$hours.'H'));
        $values = $this->portfolioValueRepository->getPortfolioValuesFrom($from);

        return $this->mapPortfolioValuesToArray($values);
    }

    /**
     * @param PortfolioValue[] $values
     */
    private function mapPortfolioValuesToArray(array $values): array
    {
        $data = [];

        foreach ($values as $value) {
            $calculatedAt = $value->getCalculatedAt();

            $data[] = [
                'time' => $calculatedAt?->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d\TH:i:s\Z'),
                'amount_usdt' => (float) $value->getAmountUsdt()->getAmount(),
            ];
        }

        return $data;
    }

    /**
     * @param Money $money
     * @return Converter
     */
    private function getConverter(Money $money): Converter
    {
        try {
            $exchange = new FixedExchange([
                $money->getCurrency()->getCode() => [
                    'USDT' => $this->binancePriceService->getExchangeRate($money->getCurrency()->getCode())
                ]
            ]);
        } catch (\Exception $e) {
            throw new \RuntimeException("Error while getting exchange rate. Error: " . $e->getMessage());
        }


        return new Converter(new CurrencyList($this->getCurrencyListData()), $exchange);
    }

    /**
     * @return array
     */
    private function getCurrencyListData(): array
    {
        return array_fill_keys($this->investmentRepository->findAllNames(), 8);
    }
}
