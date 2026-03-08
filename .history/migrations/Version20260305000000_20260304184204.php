<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260305000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create produit and categorie tables';
    }

    public function up(Schema $schema): void
    {
        // Create categorie table
        $this->addSql('CREATE TABLE categorie (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');

        // Create produit table
        $this->addSql('CREATE TABLE produit (id INT AUTO_INCREMENT NOT NULL, id_utilisateur INT NOT NULL, categorie_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, prix NUMERIC(10, 2) NOT NULL, quantite INT NOT NULL, image VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_7BFE5C856B3CA4B (id_utilisateur), INDEX IDX_7BFE5C8BCF5E72D (categorie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');

        // Add foreign keys
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_7BFE5C856B3CA4B FOREIGN KEY (id_utilisateur) REFERENCES user (id)');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_7BFE5C8BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_7BFE5C856B3CA4B');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_7BFE5C8BCF5E72D');
        $this->addSql('DROP TABLE produit');
        $this->addSql('DROP TABLE categorie');
    }
}

