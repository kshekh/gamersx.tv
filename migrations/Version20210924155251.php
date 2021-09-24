<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210924155251 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Allows for Home Row Items without a Row assigned';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE home_row_item MODIFY home_row_id INT');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE home_row_item MODIFY home_row_id INT NOT NULL');
    }
}
