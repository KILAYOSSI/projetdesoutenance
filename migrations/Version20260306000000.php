<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260306000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create notification table';
    }

    public function up(Schema $schema): void
    {
        // Create notification table
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, id_utilisateur INT NOT NULL, titre VARCHAR(255) NOT NULL, message LONGTEXT NOT NULL, type VARCHAR(50) NOT NULL, lu TINYINT(1) NOT NULL DEFAULT 0, created_at DATETIME NOT NULL, read_at DATETIME DEFAULT NULL, INDEX IDX_B7A5F2D16B3CA4B (id_utilisateur), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');

        // Add foreign key
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_B7A5F2D16B3CA4B FOREIGN KEY (id_utilisateur) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_B7A5F2D16B3CA4B');
        $this->addSql('DROP TABLE notification');
    }
}

