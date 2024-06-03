<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240603225448 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE partner_role DROP FOREIGN KEY FK_75DB972A76ED395');
        $this->addSql('ALTER TABLE fos_user_user_group DROP FOREIGN KEY fos_user_user_group_FK_0_0');
        $this->addSql('ALTER TABLE fos_user_user_group DROP FOREIGN KEY fos_user_user_group_FK_1_0');
        $this->addSql('DROP TABLE fos_user__group');
        $this->addSql('DROP TABLE fos_user__user');
        $this->addSql('DROP TABLE fos_user_user_group');
        $this->addSql('ALTER TABLE home_row ADD is_published_start VARCHAR(255) DEFAULT NULL, ADD is_published_end VARCHAR(255) DEFAULT NULL, DROP isPublishedStart, DROP isPublishedEnd, CHANGE options options JSON DEFAULT NULL, CHANGE timezone timezone VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE home_row_item ADD is_published_start VARCHAR(255) NOT NULL, ADD is_published_end VARCHAR(255) NOT NULL, DROP isPublishedStart, DROP isPublishedEnd, CHANGE sort_index sort_index SMALLINT NOT NULL, CHANGE sort_and_trim_options sort_and_trim_options JSON DEFAULT NULL, CHANGE topic topic JSON DEFAULT NULL, CHANGE is_unique_container is_unique_container TINYINT(1) DEFAULT 0 NOT NULL COMMENT \'0 = unique, 1 = allow repeat\'');
        $this->addSql('ALTER TABLE home_row_item_operation CHANGE streamer_id streamer_id VARCHAR(255) DEFAULT NULL COMMENT \'itemType=streamer => livestreaming_id, itemType=offline_streamer => userid\'');
        $this->addSql('ALTER TABLE master_theme CHANGE status status SMALLINT DEFAULT NULL');
        $this->addSql('ALTER TABLE partner_role DROP FOREIGN KEY FK_75DB972A76ED395');
        $this->addSql('ALTER TABLE partner_role ADD CONSTRAINT FK_75DB972A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('DROP INDEX UNIQ_8D93D64992FC23A8 ON user');
        $this->addSql('DROP INDEX UNIQ_8D93D649A0D96FBF ON user');
        $this->addSql('DROP INDEX UNIQ_8D93D649C05FB297 ON user');
        $this->addSql('ALTER TABLE user DROP username_canonical, DROP email, DROP email_canonical, DROP enabled, DROP salt, DROP last_login, DROP confirmation_token, DROP password_requested_at, DROP created_at, DROP updated_at, CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE roles roles JSON NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME ON user (username)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE fos_user__group (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(180) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci` COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_CDA27E965E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE fos_user__user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, username_canonical VARCHAR(180) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, email VARCHAR(180) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, email_canonical VARCHAR(180) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, password VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, last_login DATETIME DEFAULT NULL, confirmation_token VARCHAR(180) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci` COMMENT \'(DC2Type:array)\', created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, date_of_birth DATETIME DEFAULT NULL, firstname VARCHAR(64) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, lastname VARCHAR(64) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, website VARCHAR(64) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, biography VARCHAR(1000) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, gender VARCHAR(1) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, locale VARCHAR(8) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, timezone VARCHAR(64) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, phone VARCHAR(64) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, facebook_uid VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, facebook_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, facebook_data JSON CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, twitter_uid VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, twitter_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, twitter_data JSON CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, gplus_uid VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, gplus_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, gplus_data JSON CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, token VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, two_step_code VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, twitch_user_id VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, twitch_access_token VARCHAR(500) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, twitch_refresh_token VARCHAR(500) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, UNIQUE INDEX UNIQ_E54BFDA9A0D96FBF (email_canonical), UNIQUE INDEX UNIQ_E54BFDA992FC23A8 (username_canonical), UNIQUE INDEX UNIQ_E54BFDA9C05FB297 (confirmation_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE fos_user_user_group (user_id INT NOT NULL, group_id INT NOT NULL, INDEX IDX_B3C77447A76ED395 (user_id), INDEX IDX_B3C77447FE54D947 (group_id), PRIMARY KEY(user_id, group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE fos_user_user_group ADD CONSTRAINT fos_user_user_group_FK_0_0 FOREIGN KEY (group_id) REFERENCES fos_user__group (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fos_user_user_group ADD CONSTRAINT fos_user_user_group_FK_1_0 FOREIGN KEY (user_id) REFERENCES fos_user__user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE home_row ADD isPublishedStart VARCHAR(255) DEFAULT NULL, ADD isPublishedEnd VARCHAR(255) DEFAULT NULL, DROP is_published_start, DROP is_published_end, CHANGE options options JSON DEFAULT NULL, CHANGE timezone timezone VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE home_row_item ADD isPublishedStart VARCHAR(255) DEFAULT NULL, ADD isPublishedEnd VARCHAR(255) DEFAULT NULL, DROP is_published_start, DROP is_published_end, CHANGE sort_index sort_index SMALLINT DEFAULT NULL, CHANGE topic topic JSON DEFAULT NULL, CHANGE sort_and_trim_options sort_and_trim_options JSON DEFAULT NULL, CHANGE is_unique_container is_unique_container TINYINT(1) DEFAULT 1 NOT NULL COMMENT \'0 = unique,1 = allow repeat\'');
        $this->addSql('ALTER TABLE home_row_item_operation CHANGE streamer_id streamer_id VARCHAR(255) DEFAULT NULL COMMENT \'itemtype=streamer => livestreaming_id,itemtype=offline_streamer => userid\'');
        $this->addSql('ALTER TABLE master_theme CHANGE status status SMALLINT DEFAULT 0 COMMENT \'0-inactive, 1-active\'');
        $this->addSql('ALTER TABLE partner_role DROP FOREIGN KEY FK_75DB972A76ED395');
        $this->addSql('ALTER TABLE partner_role ADD CONSTRAINT FK_75DB972A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user__user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('DROP INDEX UNIQ_IDENTIFIER_USERNAME ON user');
        $this->addSql('ALTER TABLE user ADD username_canonical VARCHAR(180) NOT NULL, ADD email VARCHAR(180) NOT NULL, ADD email_canonical VARCHAR(180) NOT NULL, ADD enabled TINYINT(1) NOT NULL, ADD salt VARCHAR(255) DEFAULT NULL, ADD last_login DATETIME DEFAULT NULL, ADD confirmation_token VARCHAR(180) DEFAULT NULL, ADD password_requested_at DATETIME DEFAULT NULL, ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL, CHANGE id id VARCHAR(255) NOT NULL, CHANGE roles roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D64992FC23A8 ON user (username_canonical)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649A0D96FBF ON user (email_canonical)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649C05FB297 ON user (confirmation_token)');
    }
}
