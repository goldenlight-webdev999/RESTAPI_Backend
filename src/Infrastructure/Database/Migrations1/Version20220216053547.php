<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Migrations1;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220216053547 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE contact_request (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', email VARCHAR(255) NOT NULL, accept_commercial_communications TINYINT(1) NOT NULL, date_added DATETIME NOT NULL COMMENT \'(DC2Type:datetimetz_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE log_clean_task (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', user_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', o_auth_client_id INT DEFAULT NULL, file_name VARCHAR(255) NOT NULL, final_size INT DEFAULT NULL, original_size INT NOT NULL, client_ip VARCHAR(255) NOT NULL, user_agent VARCHAR(255) NOT NULL, execution_time INT NOT NULL, date_added DATETIME NOT NULL COMMENT \'(DC2Type:datetimetz_immutable)\', INDEX IDX_2756D289A76ED395 (user_id), INDEX IDX_2756D2894DAE4A33 (o_auth_client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE log_stripe_event (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', foreign_key VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, api_version VARCHAR(255) NOT NULL, date_created DATETIME NOT NULL COMMENT \'(DC2Type:datetimetz_immutable)\', date_added DATETIME NOT NULL COMMENT \'(DC2Type:datetimetz_immutable)\', data LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE oauth2_access_token (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, user_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', token VARCHAR(255) NOT NULL, expires_at INT DEFAULT NULL, scope VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_454D96735F37A13B (token), INDEX IDX_454D967319EB6921 (client_id), INDEX IDX_454D9673A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE oauth2_auth_code (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, user_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', token VARCHAR(255) NOT NULL, redirect_uri LONGTEXT NOT NULL, expires_at INT DEFAULT NULL, scope VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_1D2905B55F37A13B (token), INDEX IDX_1D2905B519EB6921 (client_id), INDEX IDX_1D2905B5A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE oauth2_client (id INT AUTO_INCREMENT NOT NULL, random_id VARCHAR(255) NOT NULL, redirect_uris LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', secret VARCHAR(255) NOT NULL, allowed_grant_types LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', name VARCHAR(255) DEFAULT NULL, user VARCHAR(255) DEFAULT \'f7a708b3-c427-4696-b9a4-4ce825f41163\' NOT NULL, logo VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE oauth2_refresh_token (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, user_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', token VARCHAR(255) NOT NULL, expires_at INT DEFAULT NULL, scope VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_4DD907325F37A13B (token), INDEX IDX_4DD9073219EB6921 (client_id), INDEX IDX_4DD90732A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stripe_customer (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', user_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', customer_key VARCHAR(255) NOT NULL, date_added DATETIME NOT NULL COMMENT \'(DC2Type:datetimetz_immutable)\', UNIQUE INDEX UNIQ_DC7E523AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stripe_customer_subscription (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', customer_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', subscription_id VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_FD825FD29395C3F3 (customer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subscription (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', user_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', payment_method_metadata LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', type VARCHAR(255) NOT NULL, subscription_status VARCHAR(255) NOT NULL, date_start DATETIME NOT NULL COMMENT \'(DC2Type:datetimetz_immutable)\', date_end DATETIME NOT NULL COMMENT \'(DC2Type:datetimetz_immutable)\', date_added DATETIME NOT NULL COMMENT \'(DC2Type:datetimetz_immutable)\', date_updated DATETIME NOT NULL COMMENT \'(DC2Type:datetimetz_immutable)\', INDEX IDX_A3C664D3A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', email VARCHAR(255) NOT NULL, user_name VARCHAR(255) NOT NULL, password VARCHAR(128) NOT NULL, accepts_commercial_communications TINYINT(1) DEFAULT \'0\' NOT NULL, roles LONGTEXT DEFAULT \'["ROLE_USER"]\' NOT NULL COMMENT \'(DC2Type:json_array)\', date_added DATETIME NOT NULL COMMENT \'(DC2Type:datetimetz_immutable)\', date_verified DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetimetz_immutable)\', UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE log_clean_task ADD CONSTRAINT FK_2756D289A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE log_clean_task ADD CONSTRAINT FK_2756D2894DAE4A33 FOREIGN KEY (o_auth_client_id) REFERENCES oauth2_client (id)');
        $this->addSql('ALTER TABLE oauth2_access_token ADD CONSTRAINT FK_454D967319EB6921 FOREIGN KEY (client_id) REFERENCES oauth2_client (id)');
        $this->addSql('ALTER TABLE oauth2_access_token ADD CONSTRAINT FK_454D9673A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE oauth2_auth_code ADD CONSTRAINT FK_1D2905B519EB6921 FOREIGN KEY (client_id) REFERENCES oauth2_client (id)');
        $this->addSql('ALTER TABLE oauth2_auth_code ADD CONSTRAINT FK_1D2905B5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE oauth2_refresh_token ADD CONSTRAINT FK_4DD9073219EB6921 FOREIGN KEY (client_id) REFERENCES oauth2_client (id)');
        $this->addSql('ALTER TABLE oauth2_refresh_token ADD CONSTRAINT FK_4DD90732A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE stripe_customer ADD CONSTRAINT FK_DC7E523AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE stripe_customer_subscription ADD CONSTRAINT FK_FD825FD29395C3F3 FOREIGN KEY (customer_id) REFERENCES stripe_customer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE log_clean_task DROP FOREIGN KEY FK_2756D2894DAE4A33');
        $this->addSql('ALTER TABLE oauth2_access_token DROP FOREIGN KEY FK_454D967319EB6921');
        $this->addSql('ALTER TABLE oauth2_auth_code DROP FOREIGN KEY FK_1D2905B519EB6921');
        $this->addSql('ALTER TABLE oauth2_refresh_token DROP FOREIGN KEY FK_4DD9073219EB6921');
        $this->addSql('ALTER TABLE stripe_customer_subscription DROP FOREIGN KEY FK_FD825FD29395C3F3');
        $this->addSql('ALTER TABLE log_clean_task DROP FOREIGN KEY FK_2756D289A76ED395');
        $this->addSql('ALTER TABLE oauth2_access_token DROP FOREIGN KEY FK_454D9673A76ED395');
        $this->addSql('ALTER TABLE oauth2_auth_code DROP FOREIGN KEY FK_1D2905B5A76ED395');
        $this->addSql('ALTER TABLE oauth2_refresh_token DROP FOREIGN KEY FK_4DD90732A76ED395');
        $this->addSql('ALTER TABLE stripe_customer DROP FOREIGN KEY FK_DC7E523AA76ED395');
        $this->addSql('ALTER TABLE subscription DROP FOREIGN KEY FK_A3C664D3A76ED395');
        $this->addSql('DROP TABLE contact_request');
        $this->addSql('DROP TABLE log_clean_task');
        $this->addSql('DROP TABLE log_stripe_event');
        $this->addSql('DROP TABLE oauth2_access_token');
        $this->addSql('DROP TABLE oauth2_auth_code');
        $this->addSql('DROP TABLE oauth2_client');
        $this->addSql('DROP TABLE oauth2_refresh_token');
        $this->addSql('DROP TABLE stripe_customer');
        $this->addSql('DROP TABLE stripe_customer_subscription');
        $this->addSql('DROP TABLE subscription');
        $this->addSql('DROP TABLE user');
    }
}
