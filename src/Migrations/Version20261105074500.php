<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261105074500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE photos DROP FOREIGN KEY `FK_876E0D9A76ED395`');
        $this->addSql('ALTER TABLE photos CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE photos ADD CONSTRAINT FK_876E0D9A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE photos DROP FOREIGN KEY FK_876E0D9A76ED395');
        $this->addSql('ALTER TABLE photos CHANGE user_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE photos ADD CONSTRAINT `FK_876E0D9A76ED395` FOREIGN KEY (user_id) REFERENCES user (id)');
    }
}
