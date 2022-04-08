<?php declare(strict_types=1);

namespace App\Infrastructure\Database\Migrations1;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180730120149 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user CHANGE roles roles JSON DEFAULT \'["ROLE_USER"]\' NOT NULL COMMENT \'(DC2Type:json_array)\'');
        $this->addSql('ALTER TABLE subscription ADD payment_method_metadata JSON DEFAULT NULL, DROP payment_method_description, CHANGE user_id user_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE log_clean_task CHANGE user_id user_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE o_auth_client_id o_auth_client_id INT DEFAULT NULL, CHANGE final_size final_size INT DEFAULT NULL');
        $this->addSql('ALTER TABLE oauth2_access_token CHANGE user_id user_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE expires_at expires_at INT DEFAULT NULL, CHANGE scope scope VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE oauth2_auth_code CHANGE user_id user_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE expires_at expires_at INT DEFAULT NULL, CHANGE scope scope VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE oauth2_client CHANGE name name VARCHAR(255) DEFAULT NULL, CHANGE logo logo VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE oauth2_refresh_token CHANGE user_id user_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE expires_at expires_at INT DEFAULT NULL, CHANGE scope scope VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE stripe_customer CHANGE user_id user_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE stripe_customer_subscription CHANGE customer_id customer_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE log_stripe_event CHANGE data data JSON NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE log_clean_task CHANGE user_id user_id CHAR(36) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:uuid)\', CHANGE o_auth_client_id o_auth_client_id INT DEFAULT NULL, CHANGE final_size final_size INT DEFAULT NULL');
        $this->addSql('ALTER TABLE log_stripe_event CHANGE data data LONGTEXT NOT NULL COLLATE utf8mb4_bin');
        $this->addSql('ALTER TABLE oauth2_access_token CHANGE user_id user_id CHAR(36) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:uuid)\', CHANGE expires_at expires_at INT DEFAULT NULL, CHANGE scope scope VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE oauth2_auth_code CHANGE user_id user_id CHAR(36) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:uuid)\', CHANGE expires_at expires_at INT DEFAULT NULL, CHANGE scope scope VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE oauth2_client CHANGE name name VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE logo logo VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE oauth2_refresh_token CHANGE user_id user_id CHAR(36) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:uuid)\', CHANGE expires_at expires_at INT DEFAULT NULL, CHANGE scope scope VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE stripe_customer CHANGE user_id user_id CHAR(36) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE stripe_customer_subscription CHANGE customer_id customer_id CHAR(36) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE subscription ADD payment_method_description VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, DROP payment_method_metadata, CHANGE user_id user_id CHAR(36) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON DEFAULT \'\'["ROLE_USER"]\'\' NOT NULL COLLATE utf8mb4_bin COMMENT \'(DC2Type:json_array)\'');
    }
}
