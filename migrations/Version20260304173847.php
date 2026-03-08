<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260304173847 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE otp_code DROP FOREIGN KEY FK_8A1D2E3F4B5A6C7D');
        $this->addSql('DROP TABLE otp_code');
        $this->addSql('ALTER TABLE ligne_commande ADD id INT AUTO_INCREMENT NOT NULL, ADD quantite INT NOT NULL, ADD prix_unitaire NUMERIC(10, 2) NOT NULL, CHANGE commande_id commande_id INT DEFAULT NULL, CHANGE produit_id produit_id INT DEFAULT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE otp_code (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, code VARCHAR(6) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL, expires_at DATETIME NOT NULL, is_used TINYINT(1) DEFAULT 0 NOT NULL, INDEX IDX_8A1D2E3F4B5A6C7D (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE otp_code ADD CONSTRAINT FK_8A1D2E3F4B5A6C7D FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE ligne_commande MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX `PRIMARY` ON ligne_commande');
        $this->addSql('ALTER TABLE ligne_commande DROP id, DROP quantite, DROP prix_unitaire, CHANGE commande_id commande_id INT NOT NULL, CHANGE produit_id produit_id INT NOT NULL');
        $this->addSql('ALTER TABLE ligne_commande ADD PRIMARY KEY (commande_id, produit_id)');
    }
}
