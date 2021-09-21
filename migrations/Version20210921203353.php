<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210921203353 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Adds the isPublished flag to HomeRow and HomeRowItem';
    }

    public function up(Schema $schema) : void
    {
        // Add to HomeRow and set all to published
        $this->addSql('ALTER TABLE home_row ADD COLUMN is_published BOOLEAN');
        $this->addSql('UPDATE home_row SET is_published = TRUE');
        $this->addSql('ALTER TABLE home_row MODIFY is_published BOOLEAN NOT NULL');

        // Add to HomeRowItem and set all to published
        $this->addSql('ALTER TABLE home_row_item ADD COLUMN is_published BOOLEAN');
        $this->addSql('UPDATE home_row_item SET is_published = TRUE');
        $this->addSql('ALTER TABLE home_row_item MODIFY is_published BOOLEAN NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // Drop is_published columns
        $this->addSql('ALTER TABLE home_row DROP COLUMN is_published');
        $this->addSql('ALTER TABLE home_row_item DROP COLUMN is_published');
    }
}
