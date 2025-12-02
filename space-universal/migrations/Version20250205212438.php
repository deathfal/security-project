<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20250205212438 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DROP INDEX idx_d4e6f81a76ed395');
        $this->addSql('ALTER TABLE address ALTER user_id DROP NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D4E6F81A76ED395 ON address (user_id)');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT fk_8d93d649f5b7af75');
        $this->addSql('DROP INDEX uniq_8d93d649f5b7af75');
        $this->addSql('ALTER TABLE "user" DROP address_id');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "user" ADD address_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT fk_8d93d649f5b7af75 FOREIGN KEY (address_id) REFERENCES address (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_8d93d649f5b7af75 ON "user" (address_id)');
        $this->addSql('DROP INDEX UNIQ_D4E6F81A76ED395');
        $this->addSql('ALTER TABLE address ALTER user_id SET NOT NULL');
        $this->addSql('CREATE INDEX idx_d4e6f81a76ed395 ON address (user_id)');
    }
}
