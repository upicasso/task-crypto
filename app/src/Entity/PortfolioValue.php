<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\PortfolioValueRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Money\Currency;
use Money\Money;

#[ORM\Entity(repositoryClass: PortfolioValueRepository::class)]
class PortfolioValue
{
    /**
     * Name of currency in which calculate portfolio's value
     */
    const PORTFOLIO_VALUE_CURRENCY = 'USDT';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $calculatedAt = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 20, scale: 10)]
    private ?string $amountUsdt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCalculatedAt(): ?\DateTimeImmutable
    {
        return $this->calculatedAt;
    }

    public function setCalculatedAt(\DateTimeImmutable $calculatedAt): static
    {
        $this->calculatedAt = $calculatedAt;

        return $this;
    }

    public function getAmountUsdt(): Money
    {
        return new Money($this->amountUsdt, new Currency(self::PORTFOLIO_VALUE_CURRENCY));
    }

    public function setAmountUsdt(Money $amountUsdt): static
    {
        $this->amountUsdt = $amountUsdt->getAmount();

        return $this;
    }
}
