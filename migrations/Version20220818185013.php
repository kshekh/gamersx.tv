<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220818185013 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE fos_user__group (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_CDA27E965E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fos_user__user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, username_canonical VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, email_canonical VARCHAR(180) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, confirmation_token VARCHAR(180) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, date_of_birth DATETIME DEFAULT NULL, firstname VARCHAR(64) DEFAULT NULL, lastname VARCHAR(64) DEFAULT NULL, website VARCHAR(64) DEFAULT NULL, biography VARCHAR(1000) DEFAULT NULL, gender VARCHAR(1) DEFAULT NULL, locale VARCHAR(8) DEFAULT NULL, timezone VARCHAR(64) DEFAULT NULL, phone VARCHAR(64) DEFAULT NULL, facebook_uid VARCHAR(255) DEFAULT NULL, facebook_name VARCHAR(255) DEFAULT NULL, facebook_data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', twitter_uid VARCHAR(255) DEFAULT NULL, twitter_name VARCHAR(255) DEFAULT NULL, twitter_data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', gplus_uid VARCHAR(255) DEFAULT NULL, gplus_name VARCHAR(255) DEFAULT NULL, gplus_data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', token VARCHAR(255) DEFAULT NULL, two_step_code VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_E54BFDA992FC23A8 (username_canonical), UNIQUE INDEX UNIQ_E54BFDA9A0D96FBF (email_canonical), UNIQUE INDEX UNIQ_E54BFDA9C05FB297 (confirmation_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fos_user_user_group (user_id INT NOT NULL, group_id INT NOT NULL, INDEX IDX_B3C77447A76ED395 (user_id), INDEX IDX_B3C77447FE54D947 (group_id), PRIMARY KEY(user_id, group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE home_row (id INT AUTO_INCREMENT NOT NULL, partner_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, sort_index SMALLINT DEFAULT NULL, layout VARCHAR(32) NOT NULL, options LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', is_published TINYINT(1) NOT NULL, is_glow_styling VARCHAR(50) NOT NULL, is_corner_cut VARCHAR(50) DEFAULT \'0\' NOT NULL, isPublishedStart INT DEFAULT NULL, isPublishedEnd INT DEFAULT NULL, on_gamers_xtv TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX IDX_16758CEF9393F8FE (partner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE home_row_item (id INT AUTO_INCREMENT NOT NULL, home_row_id INT DEFAULT NULL, partner_id INT DEFAULT NULL, label VARCHAR(255) DEFAULT NULL, sort_index SMALLINT DEFAULT NULL, item_type VARCHAR(32) NOT NULL, topic LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', sort_and_trim_options LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', show_art TINYINT(1) NOT NULL, custom_art VARCHAR(255) DEFAULT NULL, overlay_art VARCHAR(255) DEFAULT NULL, offline_display_type VARCHAR(32) NOT NULL, link_type VARCHAR(32) NOT NULL, custom_link VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, is_published TINYINT(1) NOT NULL, isPublishedStart INT DEFAULT NULL, isPublishedEnd INT DEFAULT NULL, is_partner TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX IDX_19713E473A6004F (home_row_id), INDEX IDX_19713E49393F8FE (partner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE partner (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE partner_role (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, partner_id INT NOT NULL, role VARCHAR(255) NOT NULL, INDEX IDX_75DB972A76ED395 (user_id), INDEX IDX_75DB9729393F8FE (partner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE site_settings (id INT AUTO_INCREMENT NOT NULL, disable_home_access TINYINT(1) DEFAULT \'1\' NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE theme (id INT AUTO_INCREMENT NOT NULL, topic_id VARCHAR(32) NOT NULL, label VARCHAR(255) DEFAULT NULL, item_type VARCHAR(32) NOT NULL, banner_image VARCHAR(255) DEFAULT NULL, embed_background VARCHAR(255) DEFAULT NULL, custom_art VARCHAR(255) DEFAULT NULL, art_background VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE fos_user_user_group ADD CONSTRAINT FK_B3C77447A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user__user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fos_user_user_group ADD CONSTRAINT FK_B3C77447FE54D947 FOREIGN KEY (group_id) REFERENCES fos_user__group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE home_row ADD CONSTRAINT FK_16758CEF9393F8FE FOREIGN KEY (partner_id) REFERENCES partner (id)');
        $this->addSql('ALTER TABLE home_row_item ADD CONSTRAINT FK_19713E473A6004F FOREIGN KEY (home_row_id) REFERENCES home_row (id)');
        $this->addSql('ALTER TABLE home_row_item ADD CONSTRAINT FK_19713E49393F8FE FOREIGN KEY (partner_id) REFERENCES partner (id)');
        $this->addSql('ALTER TABLE partner_role ADD CONSTRAINT FK_75DB972A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user__user (id)');
        $this->addSql('ALTER TABLE partner_role ADD CONSTRAINT FK_75DB9729393F8FE FOREIGN KEY (partner_id) REFERENCES partner (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fos_user_user_group DROP FOREIGN KEY FK_B3C77447FE54D947');
        $this->addSql('ALTER TABLE fos_user_user_group DROP FOREIGN KEY FK_B3C77447A76ED395');
        $this->addSql('ALTER TABLE partner_role DROP FOREIGN KEY FK_75DB972A76ED395');
        $this->addSql('ALTER TABLE home_row_item DROP FOREIGN KEY FK_19713E473A6004F');
        $this->addSql('ALTER TABLE home_row DROP FOREIGN KEY FK_16758CEF9393F8FE');
        $this->addSql('ALTER TABLE home_row_item DROP FOREIGN KEY FK_19713E49393F8FE');
        $this->addSql('ALTER TABLE partner_role DROP FOREIGN KEY FK_75DB9729393F8FE');
        $this->addSql('DROP TABLE fos_user__group');
        $this->addSql('DROP TABLE fos_user__user');
        $this->addSql('DROP TABLE fos_user_user_group');
        $this->addSql('DROP TABLE home_row');
        $this->addSql('DROP TABLE home_row_item');
        $this->addSql('DROP TABLE partner');
        $this->addSql('DROP TABLE partner_role');
        $this->addSql('DROP TABLE site_settings');
        $this->addSql('DROP TABLE theme');
    }
}
