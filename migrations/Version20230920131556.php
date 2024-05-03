<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230920131556 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE home_row_item_operation ADD is_blacklisted SMALLINT DEFAULT NULL, ADD is_full_site_blacklisted SMALLINT DEFAULT NULL, DROP is_backlisted, DROP is_full_site_backlisted');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE home_row_item_operation ADD is_backlisted SMALLINT DEFAULT NULL, ADD is_full_site_backlisted SMALLINT DEFAULT NULL, DROP is_blacklisted, DROP is_full_site_blacklisted');
    }
}
