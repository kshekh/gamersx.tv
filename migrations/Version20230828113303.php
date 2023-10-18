<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230828113303 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE master_setting ADD master_theme_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE master_setting ADD CONSTRAINT FK_435355F01AB7BA00 FOREIGN KEY (master_theme_id) REFERENCES master_theme (id)');
        $this->addSql('CREATE INDEX IDX_435355F01AB7BA00 ON master_setting (master_theme_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE master_setting DROP FOREIGN KEY FK_435355F01AB7BA00');
        $this->addSql('DROP INDEX IDX_435355F01AB7BA00 ON master_setting');
        $this->addSql('ALTER TABLE master_setting DROP master_theme_id');
    }
}
