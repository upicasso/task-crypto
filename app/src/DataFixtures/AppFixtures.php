<?php
declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Investment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Money\Currency;
use Money\Money;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        foreach ($this->getInvestmentsList() as $investmentName => $investmentValue) {
            $investment = new Investment();
            $investment->setName($investmentName);
            $investment->setValue(new Money($investmentValue, new Currency($investmentName)));

            $manager->persist($investment);
        }

        $manager->flush();
    }

    /**
     * @return string[]
     */
    public function getInvestmentsList(): array
    {
        return [
            'BTC' => '1',
            'ETH' => '10',
            'SOL' => '50',
            'USDT' => '5000',
        ];
    }
}
