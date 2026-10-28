<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261028161040 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE photo DROP FOREIGN KEY `FK_14B7841888194DE8`');
        $this->addSql('ALTER TABLE photo DROP FOREIGN KEY `FK_14B78418A76ED395`');
        $this->addSql('DROP TABLE photo');
        $this->addSql('ALTER TABLE photos ADD oeuvre_id INT DEFAULT NULL, ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE photos ADD CONSTRAINT FK_876E0D988194DE8 FOREIGN KEY (oeuvre_id) REFERENCES oeuvre (id)');
        $this->addSql('ALTER TABLE photos ADD CONSTRAINT FK_876E0D9A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_876E0D988194DE8 ON photos (oeuvre_id)');
        $this->addSql('CREATE INDEX IDX_876E0D9A76ED395 ON photos (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE photo (id INT AUTO_INCREMENT NOT NULL, oeuvre_id INT DEFAULT NULL, user_id INT DEFAULT NULL, INDEX IDX_14B7841888194DE8 (oeuvre_id), INDEX IDX_14B78418A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_uca1400_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE photo ADD CONSTRAINT `FK_14B7841888194DE8` FOREIGN KEY (oeuvre_id) REFERENCES oeuvre (id)');
        $this->addSql('ALTER TABLE photo ADD CONSTRAINT `FK_14B78418A76ED395` FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE photos DROP FOREIGN KEY FK_876E0D988194DE8');
        $this->addSql('ALTER TABLE photos DROP FOREIGN KEY FK_876E0D9A76ED395');
        $this->addSql('DROP INDEX IDX_876E0D988194DE8 ON photos');
        $this->addSql('DROP INDEX IDX_876E0D9A76ED395 ON photos');
        $this->addSql('ALTER TABLE photos DROP oeuvre_id, DROP user_id');
    }
}
