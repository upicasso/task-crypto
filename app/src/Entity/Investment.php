<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\InvestmentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Money\Currency;
use Money\Money;

#[ORM\Entity(repositoryClass: InvestmentRepository::class)]
class Investment
{
    /**
     * @var int|null
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var string|null
     */
    #[ORM\Column(length: 255, unique: true)]
    private ?string $name = null;

    /**
     * @var float|null
     */
    #[ORM\Column(type: Types::DECIMAL, precision: 20, scale: 10)]
    private ?float $value = null;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Money|null
     */
    public function getValue(): ?Money
    {
        return new Money($this->value, new Currency($this->name));
    }

    /**
     * @param Money $value
     * @return $this
     */
    public function setValue(Money $value): static
    {
        $this->value = (float) $value->getAmount();

        return $this;
    }
}
