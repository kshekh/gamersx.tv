<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230330222849 extends AbstractMigration
{
   public function getDescription(): string
   {
      return '';
   }

   public function up(Schema $schema): void
   {
      $this->addSql('ALTER TABLE home_row ADD timezone VARCHAR(255) DEFAULT NULL');
      $this->addSql('ALTER TABLE home_row_item ADD timezone VARCHAR(255) DEFAULT NULL');
      $this->addSql('ALTER TABLE home_row_item DROP updated_at');
   }

   public function down(Schema $schema): void
   {
      $this->addSql('ALTER TABLE home_row DROP timezone');
      $this->addSql('ALTER TABLE home_row_item DROP timezone');
      $this->addSql('ALTER TABLE home_row_item ADD updated_at DATETIME DEFAULT NULL');
   }
}