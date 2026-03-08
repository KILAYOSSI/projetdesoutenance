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
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Céréales')");
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Maïs')");
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Riz')");
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Mil')");
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Sorgho')");
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Blé')");
        
        // Légumes
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Légumes')");
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Tomates')");
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Oignons')");
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Piments')");
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Haricots')");
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Gombo')");
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Carottes')");
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Salades')");
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Aubergines')");
        
        // Fruits
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Fruits')");
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Bananes')");
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Mangues')");
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Ananas')");
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Oranges')");
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Papayes')");
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Pastèques')");
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Grenades')");
        
        // Tubercules
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Tubercules')");
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Manioc')");
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Ignames')");
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Patates douces')");
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Taro')");
        
        // Élevage
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Élevage')");
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Volailles')");
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Poules')");
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Canards')");
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Pintades')");
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Bétail')");
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Bœufs')");
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Chèvres')");
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Moutons')");
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Porcs')");
        
        // Cultures industrielles
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Cultures industrielles')");
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Arachides')");
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Soja')");
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Cotonniers')");
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Coton')");
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Palmiers à huile')");
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Cacaoyers')");
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Caféiers')");
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Hévéas')");
        
        // Épices et aromates
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Épices et aromates')");
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Gingembre')");
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Curcuma')");
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Poivre')");
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Clou de girofle')");
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Cannelle')");
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Basilic')");
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Menthe')");
        
        // Graines et oléagineux
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Graines et oléagineux')");
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Sésame')");
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Tournesol')");
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Arachide')");
        
        // Autres produits
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Autres produits')");
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Farines')");
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Huiles végétales')");
        $this->addSql("INSERT INTO categorie (nom) VALUES ('Produits transformés')");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DELETE FROM categorie');
    }
}
