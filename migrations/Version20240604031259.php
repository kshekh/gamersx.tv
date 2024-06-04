<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240604031259 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE home_row (id INT AUTO_INCREMENT NOT NULL, partner_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, sort_index SMALLINT DEFAULT NULL, layout VARCHAR(32) NOT NULL, options JSON DEFAULT NULL, is_published TINYINT(1) NOT NULL, is_glow_styling VARCHAR(50) NOT NULL, is_corner_cut VARCHAR(50) DEFAULT \'0\' NOT NULL, timezone VARCHAR(255) NOT NULL, is_published_start VARCHAR(255) DEFAULT NULL, is_published_end VARCHAR(255) DEFAULT NULL, on_gamers_xtv TINYINT(1) DEFAULT 0 NOT NULL, row_padding_top INT DEFAULT 0, row_padding_bottom INT DEFAULT 0, INDEX IDX_16758CEF9393F8FE (partner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE home_row_item (id INT AUTO_INCREMENT NOT NULL, home_row_id INT DEFAULT NULL, partner_id INT DEFAULT NULL, label VARCHAR(255) DEFAULT NULL, sort_index SMALLINT NOT NULL, item_type VARCHAR(32) NOT NULL, video_id VARCHAR(255) DEFAULT NULL, playlist_id VARCHAR(255) DEFAULT NULL, topic JSON DEFAULT NULL, sort_and_trim_options JSON DEFAULT NULL, show_art TINYINT(1) NOT NULL, custom_art VARCHAR(255) DEFAULT NULL, overlay_art VARCHAR(255) DEFAULT NULL, offline_display_type VARCHAR(32) NOT NULL, link_type VARCHAR(32) NOT NULL, custom_link VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, is_published TINYINT(1) NOT NULL, timezone VARCHAR(255) DEFAULT NULL, is_published_start VARCHAR(255) NOT NULL, is_published_end VARCHAR(255) NOT NULL, updated_at DATETIME NOT NULL, is_partner TINYINT(1) DEFAULT 0 NOT NULL, is_unique_container TINYINT(1) DEFAULT 0 NOT NULL COMMENT \'0 = unique, 1 = allow repeat\', INDEX IDX_19713E473A6004F (home_row_id), INDEX IDX_19713E49393F8FE (partner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE home_row_item_operation (id INT AUTO_INCREMENT NOT NULL, home_row_item_id INT DEFAULT NULL, item_type VARCHAR(25) NOT NULL, streamer_id VARCHAR(255) DEFAULT NULL COMMENT \'itemType=streamer => livestreaming_id, itemType=offline_streamer => userid\', game_id VARCHAR(255) DEFAULT NULL, priority INT DEFAULT NULL, is_whitelisted SMALLINT DEFAULT NULL, is_blacklisted SMALLINT DEFAULT NULL, is_full_site_blacklisted SMALLINT DEFAULT NULL, streamer_name VARCHAR(255) DEFAULT NULL, game_name VARCHAR(255) DEFAULT NULL, viewer VARCHAR(255) DEFAULT NULL, user_id VARCHAR(255) DEFAULT NULL, INDEX IDX_7C6B6293C72EFA38 (home_row_item_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE master_setting (id INT AUTO_INCREMENT NOT NULL, master_theme_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, value LONGTEXT DEFAULT NULL, INDEX IDX_435355F01AB7BA00 (master_theme_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE master_theme (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, status SMALLINT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE partner (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE partner_role (id INT AUTO_INCREMENT NOT NULL, partner_id INT NOT NULL, user_id INT NOT NULL, role VARCHAR(255) NOT NULL, INDEX IDX_75DB9729393F8FE (partner_id), INDEX IDX_75DB972A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE site_settings (id INT AUTO_INCREMENT NOT NULL, disable_home_access TINYINT(1) DEFAULT 1 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE theme (id INT AUTO_INCREMENT NOT NULL, topic_id VARCHAR(32) NOT NULL, label VARCHAR(255) DEFAULT NULL, item_type VARCHAR(32) NOT NULL, banner_image VARCHAR(255) DEFAULT NULL, embed_background VARCHAR(255) DEFAULT NULL, custom_art VARCHAR(255) DEFAULT NULL, art_background VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, twitch_user_id VARCHAR(255) NOT NULL, twitch_access_token VARCHAR(500) NOT NULL, twitch_refresh_token VARCHAR(500) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE home_row ADD CONSTRAINT FK_16758CEF9393F8FE FOREIGN KEY (partner_id) REFERENCES partner (id)');
        $this->addSql('ALTER TABLE home_row_item ADD CONSTRAINT FK_19713E473A6004F FOREIGN KEY (home_row_id) REFERENCES home_row (id)');
        $this->addSql('ALTER TABLE home_row_item ADD CONSTRAINT FK_19713E49393F8FE FOREIGN KEY (partner_id) REFERENCES partner (id)');
        $this->addSql('ALTER TABLE home_row_item_operation ADD CONSTRAINT FK_7C6B6293C72EFA38 FOREIGN KEY (home_row_item_id) REFERENCES home_row_item (id)');
        $this->addSql('ALTER TABLE master_setting ADD CONSTRAINT FK_435355F01AB7BA00 FOREIGN KEY (master_theme_id) REFERENCES master_theme (id)');
        $this->addSql('ALTER TABLE partner_role ADD CONSTRAINT FK_75DB9729393F8FE FOREIGN KEY (partner_id) REFERENCES partner (id)');
        $this->addSql('ALTER TABLE partner_role ADD CONSTRAINT FK_75DB972A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE home_row DROP FOREIGN KEY FK_16758CEF9393F8FE');
        $this->addSql('ALTER TABLE home_row_item DROP FOREIGN KEY FK_19713E473A6004F');
        $this->addSql('ALTER TABLE home_row_item DROP FOREIGN KEY FK_19713E49393F8FE');
        $this->addSql('ALTER TABLE home_row_item_operation DROP FOREIGN KEY FK_7C6B6293C72EFA38');
        $this->addSql('ALTER TABLE master_setting DROP FOREIGN KEY FK_435355F01AB7BA00');
        $this->addSql('ALTER TABLE partner_role DROP FOREIGN KEY FK_75DB9729393F8FE');
        $this->addSql('ALTER TABLE partner_role DROP FOREIGN KEY FK_75DB972A76ED395');
        $this->addSql('DROP TABLE home_row');
        $this->addSql('DROP TABLE home_row_item');
        $this->addSql('DROP TABLE home_row_item_operation');
        $this->addSql('DROP TABLE master_setting');
        $this->addSql('DROP TABLE master_theme');
        $this->addSql('DROP TABLE partner');
        $this->addSql('DROP TABLE partner_role');
        $this->addSql('DROP TABLE site_settings');
        $this->addSql('DROP TABLE theme');
        $this->addSql('DROP TABLE user');
    }
}
