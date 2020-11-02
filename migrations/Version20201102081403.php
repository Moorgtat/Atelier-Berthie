<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201102081403 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categorie (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, titre VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE produit (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, soustitre VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, couverture VARCHAR(255) NOT NULL, image_1 VARCHAR(255) NOT NULL, image_2 VARCHAR(255) NOT NULL, image_3 VARCHAR(255) NOT NULL, description CLOB NOT NULL, prix DOUBLE PRECISION NOT NULL)');
        $this->addSql('CREATE TABLE produit_categorie (produit_id INTEGER NOT NULL, categorie_id INTEGER NOT NULL, PRIMARY KEY(produit_id, categorie_id))');
        $this->addSql('CREATE INDEX IDX_CDEA88D8F347EFB ON produit_categorie (produit_id)');
        $this->addSql('CREATE INDEX IDX_CDEA88D8BCF5E72D ON produit_categorie (categorie_id)');
        $this->addSql('DROP INDEX IDX_CDFC735612469DE2');
        $this->addSql('DROP INDEX IDX_CDFC73564584665A');
        $this->addSql('CREATE TEMPORARY TABLE __temp__product_category AS SELECT product_id, category_id FROM product_category');
        $this->addSql('DROP TABLE product_category');
        $this->addSql('CREATE TABLE product_category (product_id INTEGER NOT NULL, category_id INTEGER NOT NULL, PRIMARY KEY(product_id, category_id), CONSTRAINT FK_CDFC73564584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_CDFC735612469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO product_category (product_id, category_id) SELECT product_id, category_id FROM __temp__product_category');
        $this->addSql('DROP TABLE __temp__product_category');
        $this->addSql('CREATE INDEX IDX_CDFC735612469DE2 ON product_category (category_id)');
        $this->addSql('CREATE INDEX IDX_CDFC73564584665A ON product_category (product_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE categorie');
        $this->addSql('DROP TABLE produit');
        $this->addSql('DROP TABLE produit_categorie');
        $this->addSql('DROP INDEX IDX_CDFC73564584665A');
        $this->addSql('DROP INDEX IDX_CDFC735612469DE2');
        $this->addSql('CREATE TEMPORARY TABLE __temp__product_category AS SELECT product_id, category_id FROM product_category');
        $this->addSql('DROP TABLE product_category');
        $this->addSql('CREATE TABLE product_category (product_id INTEGER NOT NULL, category_id INTEGER NOT NULL, PRIMARY KEY(product_id, category_id))');
        $this->addSql('INSERT INTO product_category (product_id, category_id) SELECT product_id, category_id FROM __temp__product_category');
        $this->addSql('DROP TABLE __temp__product_category');
        $this->addSql('CREATE INDEX IDX_CDFC73564584665A ON product_category (product_id)');
        $this->addSql('CREATE INDEX IDX_CDFC735612469DE2 ON product_category (category_id)');
    }
}
