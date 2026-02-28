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
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

/**
 *
 */
final class PortfolioValuationServiceTest extends TestCase
{
    /**
     * @return void
     * @throws Exception
     */
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

        $investmentRepository->method('findAll')
            ->willReturn($this->getInvestmentMocks());

        $investmentRepository->method('findAllNames')
            ->willReturn(['BTC', 'USDT']);

        $binance->expects($this->once())
            ->method('getExchangeRate')
            ->willReturn(2.0);

        $result = $service->createNewPortfolioValuation();
        $amount = $result->getAmountUsdt();

        self::assertSame('USDT', $amount->getCurrency()->getCode());
        // 100 * 2 (BTC->USDT) + 50 USDT
        self::assertSame('250', $amount->getAmount());
    }

    /**
     * @return void
     * @throws Exception
     */
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

        $portfolioValueRepository->method('getPortfolioValues')
            ->willReturn($this->getPortfolioValueMocks());

        $result = $service->getPortfolioHistory();

        self::assertCount(2, $result);
        self::assertSame('2026-02-26T10:00:00Z', $result[0]['time']);
        self::assertSame(12345678.0, $result[0]['amount_usdt']);
    }

    /**
     * @return PortfolioValue[]
     */
    private function getPortfolioValueMocks(): array
    {
        $firstPortfolioValue = new PortfolioValue();
        $firstPortfolioValue->setCalculatedAt(new \DateTimeImmutable('2026-02-26T10:00:00Z'));
        $firstPortfolioValue->setAmountUsdt(new Money('12345678', new Currency('USDT')));

        $secondPortfolioValue = new PortfolioValue();
        $secondPortfolioValue->setCalculatedAt(new \DateTimeImmutable('2026-02-26T10:00:00Z'));
        $secondPortfolioValue->setAmountUsdt(new Money('12345678', new Currency('USDT')));

        return [$firstPortfolioValue, $secondPortfolioValue];
    }
    /**
     * @return array
     * @throws Exception
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

