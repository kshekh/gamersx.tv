<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230419130328 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE fos_user__user ADD twitch_user_id VARCHAR(255) DEFAULT NULL, ADD twitch_access_token VARCHAR(500) DEFAULT NULL, ADD twitch_refresh_token VARCHAR(500) DEFAULT NULL');

    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE fos_user__user DROP twitch_user_id, DROP twitch_access_token, DROP twitch_refresh_token');

    }
}
