<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261104075737 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE artiste CHANGE nationalite nationalite VARCHAR(100) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9C07354F1FE3BDAF ON artiste (pseudonyme)');
        $this->addSql('ALTER TABLE lieu CHANGE adresse adresse VARCHAR(255) DEFAULT NULL, CHANGE latitude latitude DOUBLE PRECISION NOT NULL, CHANGE longitude longitude DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE oeuvre ADD recouvre_id INT DEFAULT NULL, DROP oeuvre_recouverte, DROP latitude, DROP longitude, CHANGE date_creation date_creation DATE DEFAULT NULL, CHANGE lieu_id lieu_id INT NOT NULL');
        $this->addSql('ALTER TABLE oeuvre ADD CONSTRAINT FK_35FE2EFEE3E32370 FOREIGN KEY (recouvre_id) REFERENCES oeuvre (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_35FE2EFEE3E32370 ON oeuvre (recouvre_id)');
        $this->addSql('ALTER TABLE photos CHANGE etat_oeuvre etat_oeuvre VARCHAR(255) DEFAULT NULL, CHANGE oeuvre_id oeuvre_id INT NOT NULL, CHANGE user_id user_id INT NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D73B98416C6E55B5 ON technique (nom)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_9C07354F1FE3BDAF ON artiste');
        $this->addSql('ALTER TABLE artiste CHANGE nationalite nationalite VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE lieu CHANGE adresse adresse VARCHAR(255) NOT NULL, CHANGE latitude latitude DOUBLE PRECISION DEFAULT NULL, CHANGE longitude longitude DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE oeuvre DROP FOREIGN KEY FK_35FE2EFEE3E32370');
        $this->addSql('DROP INDEX IDX_35FE2EFEE3E32370 ON oeuvre');
        $this->addSql('ALTER TABLE oeuvre ADD oeuvre_recouverte TINYINT NOT NULL, ADD latitude DOUBLE PRECISION DEFAULT NULL, ADD longitude DOUBLE PRECISION DEFAULT NULL, DROP recouvre_id, CHANGE date_creation date_creation DATE NOT NULL, CHANGE lieu_id lieu_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE photos CHANGE etat_oeuvre etat_oeuvre VARCHAR(255) NOT NULL, CHANGE oeuvre_id oeuvre_id INT DEFAULT NULL, CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('DROP INDEX UNIQ_D73B98416C6E55B5 ON technique');
    }
}
