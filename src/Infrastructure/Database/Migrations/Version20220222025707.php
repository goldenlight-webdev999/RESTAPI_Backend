<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220222025707 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE log_paypal_event (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', foreign_key VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, api_version VARCHAR(255) NOT NULL, date_created DATETIME NOT NULL COMMENT \'(DC2Type:datetimetz_immutable)\', date_added DATETIME NOT NULL COMMENT \'(DC2Type:datetimetz_immutable)\', data LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE paypal_customer (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', user_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', customer_key VARCHAR(255) NOT NULL, date_added DATETIME NOT NULL COMMENT \'(DC2Type:datetimetz_immutable)\', UNIQUE INDEX UNIQ_CA07324DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE paypal_customer_subscription (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', customer_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', subscription_id VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_BD88D4DF9395C3F3 (customer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE paypal_customer ADD CONSTRAINT FK_CA07324DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE paypal_customer_subscription ADD CONSTRAINT FK_BD88D4DF9395C3F3 FOREIGN KEY (customer_id) REFERENCES paypal_customer (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE log_task');
        $this->addSql('DROP TABLE migration_versions');
        $this->addSql('ALTER TABLE log_stripe_event CHANGE data data LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE subscription CHANGE payment_method_metadata payment_method_metadata LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE paypal_customer_subscription DROP FOREIGN KEY FK_BD88D4DF9395C3F3');
        $this->addSql('CREATE TABLE log_task (id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', user_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', client_id INT DEFAULT NULL, file_name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, final_size INT DEFAULT NULL, original_size INT NOT NULL, client_ip VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, user_agent VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, date_added DATETIME NOT NULL COMMENT \'(DC2Type:datetimetz_immutable)\', INDEX IDX_43103550A76ED395 (user_id), INDEX IDX_4310355019EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE migration_versions (version VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, PRIMARY KEY(version)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE log_task ADD CONSTRAINT FK_4310355019EB6921 FOREIGN KEY (client_id) REFERENCES oauth2_client (id)');
        $this->addSql('ALTER TABLE log_task ADD CONSTRAINT FK_43103550A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('DROP TABLE log_paypal_event');
        $this->addSql('DROP TABLE paypal_customer');
        $this->addSql('DROP TABLE paypal_customer_subscription');
        $this->addSql('ALTER TABLE log_stripe_event CHANGE data data LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE subscription CHANGE payment_method_metadata payment_method_metadata LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_bin`');
    }
}
