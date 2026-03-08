<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260226000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add confirmation code fields to User entity';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user ADD confirmation_code VARCHAR(6) DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD code_expires_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user DROP COLUMN confirmation_code');
        $this->addSql('ALTER TABLE user DROP COLUMN code_expires_at');
    }
}
