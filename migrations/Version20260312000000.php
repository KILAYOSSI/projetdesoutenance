<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260312000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add motif column to Kyc table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE kyc ADD motif LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE kyc DROP motif');
    }
}

