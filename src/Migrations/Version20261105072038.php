<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261105072038 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Recouvrement : la relation réflexive est remplacée par le booléen oeuvre_recouverte';
    }

    public function up(Schema $schema): void
    {
        // Le recouvrement devient un simple booléen
        $this->addSql('ALTER TABLE oeuvre ADD oeuvre_recouverte TINYINT DEFAULT 0 NOT NULL');
        // On garde l'info : une œuvre qui était recouverte par une autre passe à 1
        $this->addSql('UPDATE oeuvre o JOIN (SELECT DISTINCT recouvre_id FROM oeuvre WHERE recouvre_id IS NOT NULL) r ON o.id = r.recouvre_id SET o.oeuvre_recouverte = 1');
        $this->addSql('ALTER TABLE oeuvre DROP FOREIGN KEY `FK_35FE2EFEE3E32370`');
        $this->addSql('DROP INDEX IDX_35FE2EFEE3E32370 ON oeuvre');
        $this->addSql('ALTER TABLE oeuvre DROP recouvre_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE oeuvre ADD recouvre_id INT DEFAULT NULL, DROP oeuvre_recouverte');
        $this->addSql('ALTER TABLE oeuvre ADD CONSTRAINT `FK_35FE2EFEE3E32370` FOREIGN KEY (recouvre_id) REFERENCES oeuvre (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_35FE2EFEE3E32370 ON oeuvre (recouvre_id)');
    }
}
