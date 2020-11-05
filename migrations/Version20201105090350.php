<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201105090350 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE transporteur (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, description CLOB NOT NULL, prix DOUBLE PRECISION NOT NULL)');
        $this->addSql('DROP INDEX IDX_C35F0816A76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__adresse AS SELECT id, user_id, titre, prenom, nom, societe, numero, rue, codepostal, ville, pays, telephone FROM adresse');
        $this->addSql('DROP TABLE adresse');
        $this->addSql('CREATE TABLE adresse (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, titre VARCHAR(255) NOT NULL COLLATE BINARY, prenom VARCHAR(255) NOT NULL COLLATE BINARY, nom VARCHAR(255) NOT NULL COLLATE BINARY, societe VARCHAR(255) NOT NULL COLLATE BINARY, numero VARCHAR(255) NOT NULL COLLATE BINARY, rue VARCHAR(255) NOT NULL COLLATE BINARY, codepostal VARCHAR(255) NOT NULL COLLATE BINARY, ville VARCHAR(255) NOT NULL COLLATE BINARY, pays VARCHAR(255) NOT NULL COLLATE BINARY, telephone VARCHAR(255) NOT NULL COLLATE BINARY, CONSTRAINT FK_C35F0816A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO adresse (id, user_id, titre, prenom, nom, societe, numero, rue, codepostal, ville, pays, telephone) SELECT id, user_id, titre, prenom, nom, societe, numero, rue, codepostal, ville, pays, telephone FROM __temp__adresse');
        $this->addSql('DROP TABLE __temp__adresse');
        $this->addSql('CREATE INDEX IDX_C35F0816A76ED395 ON adresse (user_id)');
        $this->addSql('DROP INDEX IDX_CDEA88D8BCF5E72D');
        $this->addSql('DROP INDEX IDX_CDEA88D8F347EFB');
        $this->addSql('CREATE TEMPORARY TABLE __temp__produit_categorie AS SELECT produit_id, categorie_id FROM produit_categorie');
        $this->addSql('DROP TABLE produit_categorie');
        $this->addSql('CREATE TABLE produit_categorie (produit_id INTEGER NOT NULL, categorie_id INTEGER NOT NULL, PRIMARY KEY(produit_id, categorie_id), CONSTRAINT FK_CDEA88D8F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_CDEA88D8BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO produit_categorie (produit_id, categorie_id) SELECT produit_id, categorie_id FROM __temp__produit_categorie');
        $this->addSql('DROP TABLE __temp__produit_categorie');
        $this->addSql('CREATE INDEX IDX_CDEA88D8BCF5E72D ON produit_categorie (categorie_id)');
        $this->addSql('CREATE INDEX IDX_CDEA88D8F347EFB ON produit_categorie (produit_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE transporteur');
        $this->addSql('DROP INDEX IDX_C35F0816A76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__adresse AS SELECT id, user_id, titre, prenom, nom, societe, numero, rue, codepostal, ville, pays, telephone FROM adresse');
        $this->addSql('DROP TABLE adresse');
        $this->addSql('CREATE TABLE adresse (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, titre VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, societe VARCHAR(255) NOT NULL, numero VARCHAR(255) NOT NULL, rue VARCHAR(255) NOT NULL, codepostal VARCHAR(255) NOT NULL, ville VARCHAR(255) NOT NULL, pays VARCHAR(255) NOT NULL, telephone VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO adresse (id, user_id, titre, prenom, nom, societe, numero, rue, codepostal, ville, pays, telephone) SELECT id, user_id, titre, prenom, nom, societe, numero, rue, codepostal, ville, pays, telephone FROM __temp__adresse');
        $this->addSql('DROP TABLE __temp__adresse');
        $this->addSql('CREATE INDEX IDX_C35F0816A76ED395 ON adresse (user_id)');
        $this->addSql('DROP INDEX IDX_CDEA88D8F347EFB');
        $this->addSql('DROP INDEX IDX_CDEA88D8BCF5E72D');
        $this->addSql('CREATE TEMPORARY TABLE __temp__produit_categorie AS SELECT produit_id, categorie_id FROM produit_categorie');
        $this->addSql('DROP TABLE produit_categorie');
        $this->addSql('CREATE TABLE produit_categorie (produit_id INTEGER NOT NULL, categorie_id INTEGER NOT NULL, PRIMARY KEY(produit_id, categorie_id))');
        $this->addSql('INSERT INTO produit_categorie (produit_id, categorie_id) SELECT produit_id, categorie_id FROM __temp__produit_categorie');
        $this->addSql('DROP TABLE __temp__produit_categorie');
        $this->addSql('CREATE INDEX IDX_CDEA88D8F347EFB ON produit_categorie (produit_id)');
        $this->addSql('CREATE INDEX IDX_CDEA88D8BCF5E72D ON produit_categorie (categorie_id)');
    }
}
