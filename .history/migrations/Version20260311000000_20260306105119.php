<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260311000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout des catégories agricoles';
    }

    public function up(Schema $schema): void
    {
        // Céréales
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (1, 'Céréales', NOW())");
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (2, 'Maïs', NOW())");
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (3, 'Riz', NOW())");
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (4, 'Mil', NOW())");
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (5, 'Sorgho', NOW())");
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (6, 'Blé', NOW())");
        
        // Légumes
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (7, 'Légumes', NOW())");
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (8, 'Tomates', NOW())");
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (9, 'Oignons', NOW())");
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (10, 'Piments', NOW())");
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (11, 'Haricots', NOW())");
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (12, 'Gombo', NOW())");
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (13, 'Carottes', NOW())");
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (14, 'Salades', NOW())");
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (15, 'Aubergines', NOW())");
        
        // Fruits
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (16, 'Fruits', NOW())");
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (17, 'Bananes', NOW())");
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (18, 'Mangues', NOW())");
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (19, 'Ananas', NOW())");
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (20, 'Oranges', NOW())");
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (21, 'Papayes', NOW())");
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (22, 'Pastèques', NOW())");
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (23, 'Grenades', NOW())");
        
        // Tubercules
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (24, 'Tubercules', NOW())");
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (25, 'Manioc', NOW())");
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (26, 'Ignames', NOW())");
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (27, 'Patates douces', NOW())");
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (28, 'Taro', NOW())");
        
        // Élevage
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (29, 'Élevage', NOW())");
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (30, 'Volailles', NOW())");
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (31, 'Poules', NOW())");
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (32, 'Canards', NOW())");
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (33, 'Pintades', NOW())");
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (34, 'Bétail', NOW())");
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (35, 'Bœufs', NOW())");
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (36, 'Chèvres', NOW())");
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (37, 'Moutons', NOW())");
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (38, 'Porcs', NOW())");
        
        // Cultures industrielles
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (39, 'Cultures industrielles', NOW())");
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (40, 'Arachides', NOW())");
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (41, 'Soja', NOW())");
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (42, 'Cotonniers', NOW())");
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (43, 'Coton', NOW())");
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (44, 'Palmiers à huile', NOW())");
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (45, 'Cacaoyers', NOW())");
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (46, 'Caféiers', NOW())");
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (47, 'Hévéas', NOW())");
        
        // Épices et aromates
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (48, 'Épices et aromates', NOW())");
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (49, 'Gingembre', NOW())");
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (50, 'Curcuma', NOW())");
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (51, 'Poivre', NOW())");
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (52, 'Clou de girofle', NOW())");
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (53, 'Cannelle', NOW())");
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (54, 'Basilic', NOW())");
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (55, 'Menthe', NOW())");
        
        // Graines et oléagineux
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (56, 'Graines et oléagineux', NOW())");
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (57, 'Sésame', NOW())");
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (58, 'Tournesol', NOW())");
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (59, 'Arachide', NOW())");
        
        // Autres produits
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (60, 'Autres produits', NOW())");
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (61, 'Farines', NOW())");
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (62, 'Huiles végétales', NOW())");
        $this->addSql("INSERT INTO categorie (id, nom, created_at) VALUES (63, 'Produits transformés', NOW())");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DELETE FROM categorie');
    }
}
