<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220122210506 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "order" ALTER reference_stripe SET DEFAULT NULL');
        $this->addSql('ALTER TABLE "order" ALTER state SET DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD is_best BOOLEAN DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE product DROP is_best');
        $this->addSql('ALTER TABLE "order" ALTER reference_stripe DROP NOT NULL');
        $this->addSql('ALTER TABLE "order" ALTER state DROP NOT NULL');
    }
}
