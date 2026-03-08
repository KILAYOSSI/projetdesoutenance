<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260304170534 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE kyc (id INT AUTO_INCREMENT NOT NULL, id_utilisateur INT DEFAULT NULL, type_piece VARCHAR(50) NOT NULL, numero_piece VARCHAR(100) NOT NULL, photo_piece_recto VARCHAR(255) DEFAULT NULL, photo_piece_verso VARCHAR(255) DEFAULT NULL, photo_selfie VARCHAR(255) DEFAULT NULL, status VARCHAR(20) DEFAULT \'en_attente\' NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_91850F8E50EAE44 (id_utilisateur), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE kyc ADD CONSTRAINT FK_91850F8E50EAE44 FOREIGN KEY (id_utilisateur) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE kyc DROP FOREIGN KEY FK_91850F8E50EAE44');
        $this->addSql('DROP TABLE kyc');
    }
}
