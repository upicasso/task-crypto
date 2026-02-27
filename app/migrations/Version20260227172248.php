<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260227172248 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE investment (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL UNIQUE, value NUMERIC(20, 10) NOT NULL, UNIQUE INDEX UNIQ_43CA0AD65E237E06 (name), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE portfolio_value (id INT AUTO_INCREMENT NOT NULL, calculated_at DATETIME NOT NULL, amount_usdt NUMERIC(20, 10) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE investment');
        $this->addSql('DROP TABLE portfolio_value');
    }
}
