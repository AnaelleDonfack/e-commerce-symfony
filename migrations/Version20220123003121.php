<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220123003121 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE reset_password_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE reset_password (id INT NOT NULL, client_id INT NOT NULL, token VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B9983CE519EB6921 ON reset_password (client_id)');
        $this->addSql('COMMENT ON COLUMN reset_password.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE reset_password ADD CONSTRAINT FK_B9983CE519EB6921 FOREIGN KEY (client_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "order" ALTER reference_stripe SET DEFAULT NULL');
        $this->addSql('ALTER TABLE "order" ALTER state SET DEFAULT NULL');
        $this->addSql('ALTER TABLE product ALTER is_best SET DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE reset_password_id_seq CASCADE');
        $this->addSql('DROP TABLE reset_password');
        $this->addSql('ALTER TABLE product ALTER is_best DROP NOT NULL');
        $this->addSql('ALTER TABLE "order" ALTER reference_stripe DROP NOT NULL');
        $this->addSql('ALTER TABLE "order" ALTER state DROP NOT NULL');
    }
}
