<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260307000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create paiement table';
    }

    public function up(Schema $schema): void
    {
        // Create paiement table
        $this->addSql('CREATE TABLE paiement (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT NOT NULL, commande_id INT DEFAULT NULL, montant NUMERIC(10, 2) NOT NULL, statut VARCHAR(50) NOT NULL, methode VARCHAR(100) NOT NULL, reference VARCHAR(255) DEFAULT NULL, transaction_id VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, date_paiement DATETIME DEFAULT NULL, INDEX IDX_B7A5F2D1FB88E14F (utilisateur_id), INDEX IDX_B7A5F2D182EA2C54 (commande_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');

        // Add foreign keys
        $this->addSql('ALTER TABLE paiement ADD CONSTRAINT FK_B7A5F2D1FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE paiement ADD CONSTRAINT FK_B7A5F2D182EA2C54 FOREIGN KEY (commande_id) REFERENCES commande (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE paiement DROP FOREIGN KEY FK_B7A5F2D1FB88E14F');
        $this->addSql('ALTER TABLE paiement DROP FOREIGN KEY FK_B7A5F2D182EA2C54');
        $this->addSql('DROP TABLE paiement');
    }
}

