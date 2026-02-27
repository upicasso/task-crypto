<?php

namespace App\Tests\Service;

use App\Entity\PortfolioValue;
use App\Repository\InvestmentRepository;
use App\Repository\PortfolioValueRepository;
use App\Service\BinancePriceService;
use App\Service\PortfolioValuationService;
use Doctrine\ORM\EntityManagerInterface;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

final class PortfolioValuationServiceTest extends TestCase
{
    public function testCalculatePortfolioValueConvertsNonUsdtAndSums(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $binance = $this->createMock(BinancePriceService::class);
        $investmentRepository = $this->createMock(InvestmentRepository::class);
        $portfolioValueRepository = $this->createMock(PortfolioValueRepository::class);

        $service = new PortfolioValuationService(
            $entityManager,
            $binance,
            $investmentRepository,
            $portfolioValueRepository
        );

        list($btcInvestment, $usdtInvestment) = $this->getInvestmentMocks();

        $investmentRepository->method('findAll')
            ->willReturn($this->getInvestmentMocks());

        $investmentRepository->method('findAllNames')
            ->willReturn(['BTC', 'USDT']);

        $binance->method('getExchangeRate')
            ->with('BTC')
            ->willReturn(2.0);

        $result = $service->calculatePortfolioValue();

        self::assertInstanceOf(Money::class, $result);
        self::assertSame('USDT', $result->getCurrency()->getCode());
        // 100 * 2 (BTC->USDT) + 50 USDT
        self::assertSame('250', $result->getAmount());
    }

    public function testGetPortfolioHistoryMapsValues(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $binance = $this->createMock(BinancePriceService::class);
        $investmentRepository = $this->createMock(InvestmentRepository::class);
        $portfolioValueRepository = $this->createMock(PortfolioValueRepository::class);

        $service = new PortfolioValuationService(
            $entityManager,
            $binance,
            $investmentRepository,
            $portfolioValueRepository
        );

        $pv = new PortfolioValue();
        $pv->setCalculatedAt(new \DateTimeImmutable('2026-02-26T10:00:00Z'));
        $pv->setAmountUsdt(new Money('12345678', new Currency('USDT')));

        $portfolioValueRepository->method('getPortfolioValues')
            ->willReturn([$pv]);

        $result = $service->getPortfolioHistory();

        self::assertCount(1, $result);
        self::assertSame('2026-02-26T10:00:00Z', $result[0]['time']);
        self::assertSame(123456.78, $result[0]['amount_usdt']);
    }

    /**
     * @return array
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    private function getInvestmentMocks(): array
    {
        $btcMoney = new Money('100', new Currency('BTC'));
        $usdtMoney = new Money('50', new Currency('USDT'));

        $btcInvestment = $this->createMock(\App\Entity\Investment::class);
        $btcInvestment->method('getName')->willReturn('BTC');
        $btcInvestment->method('getValue')->willReturn($btcMoney);

        $usdtInvestment = $this->createMock(\App\Entity\Investment::class);
        $usdtInvestment->method('getName')->willReturn('USDT');
        $usdtInvestment->method('getValue')->willReturn($usdtMoney);

        return [$btcInvestment, $usdtInvestment];
    }
}

