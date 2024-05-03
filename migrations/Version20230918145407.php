<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230918145407 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE home_row_item_operation (id INT AUTO_INCREMENT NOT NULL, home_row_item_id INT DEFAULT NULL, item_type VARCHAR(25) NOT NULL, streamer_id VARCHAR(255) DEFAULT NULL, game_id VARCHAR(255) DEFAULT NULL, priority INT DEFAULT NULL, is_whitelisted SMALLINT DEFAULT NULL, is_backlisted SMALLINT DEFAULT NULL, is_full_site_backlisted SMALLINT DEFAULT NULL, INDEX IDX_7C6B6293C72EFA38 (home_row_item_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE home_row_item_operation ADD CONSTRAINT FK_7C6B6293C72EFA38 FOREIGN KEY (home_row_item_id) REFERENCES home_row_item (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE home_row_item_operation');
    }
}
