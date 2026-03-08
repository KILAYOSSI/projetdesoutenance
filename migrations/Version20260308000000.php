<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260308000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create exploitation, suivi_culture, equipement, stock, conseil, and rendement tables';
    }

    public function up(Schema $schema): void
    {
        // Create exploitation table
        $this->addSql('CREATE TABLE exploitation (id INT AUTO_INCREMENT NOT NULL, id_utilisateur INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, superficie NUMERIC(10, 2) DEFAULT NULL, localisation VARCHAR(255) DEFAULT NULL, type_sol VARCHAR(100) DEFAULT NULL, source_eau VARCHAR(100) DEFAULT NULL, statut VARCHAR(50) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_5A93EA766B3CA4B (id_utilisateur), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        
        // Create suivi_culture table
        $this->addSql('CREATE TABLE suivi_culture (id INT AUTO_INCREMENT NOT NULL, exploitation_id INT DEFAULT NULL, categorie_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, variete VARCHAR(100) DEFAULT NULL, superficie NUMERIC(10, 2) DEFAULT NULL, date_semis DATE DEFAULT NULL, date_recolte_prevue DATE DEFAULT NULL, date_recolte_reelle DATE DEFAULT NULL, statut VARCHAR(50) DEFAULT NULL, observations LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_9B38AC1CF4E783AF (exploitation_id), INDEX IDX_9B38AC1CBCF5E72D (categorie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        
        // Create equipement table
        $this->addSql('CREATE TABLE equipement (id INT AUTO_INCREMENT NOT NULL, exploitation_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, type VARCHAR(100) NOT NULL, etat VARCHAR(50) DEFAULT NULL, date_achat DATE DEFAULT NULL, observations LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_2B48A0F7CF4E783AF (exploitation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        
        // Create stock table
        $this->addSql('CREATE TABLE stock (id INT AUTO_INCREMENT NOT NULL, exploitation_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, type VARCHAR(100) NOT NULL, quantite NUMERIC(10, 2) NOT NULL, unite VARCHAR(50) NOT NULL, seuil_alerte NUMERIC(10, 2) DEFAULT NULL, date_expiration DATE DEFAULT NULL, observations LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_5B48A0F7CF4E783AF (exploitation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        
        // Create conseil table
        $this->addSql('CREATE TABLE conseil (id INT AUTO_INCREMENT NOT NULL, auteur_id INT DEFAULT NULL, titre VARCHAR(255) NOT NULL, contenu LONGTEXT NOT NULL, type VARCHAR(50) NOT NULL, categorie VARCHAR(100) DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, est_publie TINYINT(1) NOT NULL, date_publication DATETIME NOT NULL, vues INT DEFAULT NULL, INDEX IDX_4B8A0F1C7B70D0D7 (auteur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        
        // Create rendement table
        $this->addSql('CREATE TABLE rendement (id INT AUTO_INCREMENT NOT NULL, suivi_culture_id INT DEFAULT NULL, quantite NUMERIC(10, 2) NOT NULL, unite VARCHAR(50) NOT NULL, date_recolte DATE NOT NULL, observations LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_6C8A0F1C7B70D0D8 (suivi_culture_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');

        // Add foreign keys
        $this->addSql('ALTER TABLE exploitation ADD CONSTRAINT FK_5A93EA766B3CA4B FOREIGN KEY (id_utilisateur) REFERENCES user (id)');
        $this->addSql('ALTER TABLE suivi_culture ADD CONSTRAINT FK_9B38AC1CF4E783AF FOREIGN KEY (exploitation_id) REFERENCES exploitation (id)');
        $this->addSql('ALTER TABLE suivi_culture ADD CONSTRAINT FK_9B38AC1CBCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)');
        $this->addSql('ALTER TABLE equipement ADD CONSTRAINT FK_2B48A0F7CF4E783AF FOREIGN KEY (exploitation_id) REFERENCES exploitation (id)');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_5B48A0F7CF4E783AF FOREIGN KEY (exploitation_id) REFERENCES exploitation (id)');
        $this->addSql('ALTER TABLE conseil ADD CONSTRAINT FK_4B8A0F1C7B70D0D7 FOREIGN KEY (auteur_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE rendement ADD CONSTRAINT FK_6C8A0F1C7B70D0D8 FOREIGN KEY (suivi_culture_id) REFERENCES suivi_culture (id)');
    }

    public function down(Schema $schema): void
    {
        // Drop foreign keys
        $this->addSql('ALTER TABLE exploitation DROP FOREIGN KEY FK_5A93EA766B3CA4B');
        $this->addSql('ALTER TABLE suivi_culture DROP FOREIGN KEY FK_9B38AC1CF4E783AF');
        $this->addSql('ALTER TABLE suivi_culture DROP FOREIGN KEY FK_9B38AC1CBCF5E72D');
        $this->addSql('ALTER TABLE equipement DROP FOREIGN KEY FK_2B48A0F7CF4E783AF');
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_5B48A0F7CF4E783AF');
        $this->addSql('ALTER TABLE conseil DROP FOREIGN KEY FK_4B8A0F1C7B70D0D7');
        $this->addSql('ALTER TABLE rendement DROP FOREIGN KEY FK_6C8A0F1C7B70D0D8');
        
        // Drop tables
        $this->addSql('DROP TABLE exploitation');
        $this->addSql('DROP TABLE suivi_culture');
        $this->addSql('DROP TABLE equipement');
        $this->addSql('DROP TABLE stock');
        $this->addSql('DROP TABLE conseil');
        $this->addSql('DROP TABLE rendement');
    }
}

