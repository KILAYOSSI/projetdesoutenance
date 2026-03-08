<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260227000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create otp_code table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE otp_code (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, code VARCHAR(6) NOT NULL, created_at DATETIME NOT NULL, expires_at DATETIME NOT NULL, is_used TINYINT(1) NOT NULL DEFAULT 0, INDEX IDX_8A1D2E3F4B5A6C7D (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE otp_code ADD CONSTRAINT FK_8A1D2E3F4B5A6C7D FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE otp_code');
    }
}
