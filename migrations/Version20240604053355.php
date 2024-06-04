<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240604053355 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE home_row CHANGE is_glow_styling is_glow_styling TINYINT(1) DEFAULT 0 NOT NULL, CHANGE is_corner_cut is_corner_cut TINYINT(1) DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE home_row CHANGE is_glow_styling is_glow_styling VARCHAR(50) NOT NULL, CHANGE is_corner_cut is_corner_cut VARCHAR(50) DEFAULT \'0\' NOT NULL');
    }
}
