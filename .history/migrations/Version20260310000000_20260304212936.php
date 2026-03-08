<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260310000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add isVendeur, isKycValidated, nomComplet, telephone to User';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD is_vendeur TINYINT(1) DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE user ADD is_kyc_validated TINYINT(1) DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE user ADD nom_complet VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD telephone VARCHAR(20) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP is_vendeur');
        $this->addSql('ALTER TABLE user DROP is_kyc_validated');
        $this->addSql('ALTER TABLE user DROP nom_complet');
        $this->addSql('ALTER TABLE user DROP telephone');
    }
}

