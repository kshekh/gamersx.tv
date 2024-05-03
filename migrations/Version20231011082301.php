<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231011082301 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE home_row_item CHANGE is_unique_container is_unique_container TINYINT(1) DEFAULT \'1\' NOT NULL COMMENT \'0 = unique,1 = allow repeat\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE home_row_item CHANGE is_unique_container is_unique_container TINYINT(1) DEFAULT \'0\' NOT NULL');
    }
}
