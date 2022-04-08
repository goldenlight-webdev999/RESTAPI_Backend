<?php declare(strict_types=1);

namespace App\Infrastructure\Database\Migrations1;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180709142244 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE oauth2_client ADD name VARCHAR(255) NOT NULL, ADD logo VARCHAR(255) NOT NULL, DROP client_name, DROP client_logo');
        $this->addSql('ALTER TABLE oauth2_access_token CHANGE user_id user_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE expires_at expires_at INT DEFAULT NULL, CHANGE scope scope VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE oauth2_auth_code CHANGE user_id user_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE expires_at expires_at INT DEFAULT NULL, CHANGE scope scope VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE oauth2_refresh_token CHANGE user_id user_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE expires_at expires_at INT DEFAULT NULL, CHANGE scope scope VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE oauth2_access_token CHANGE user_id user_id CHAR(36) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:uuid)\', CHANGE expires_at expires_at INT DEFAULT NULL, CHANGE scope scope VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE oauth2_auth_code CHANGE user_id user_id CHAR(36) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:uuid)\', CHANGE expires_at expires_at INT DEFAULT NULL, CHANGE scope scope VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE oauth2_client ADD client_name VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, ADD client_logo VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, DROP name, DROP logo');
        $this->addSql('ALTER TABLE oauth2_refresh_token CHANGE user_id user_id CHAR(36) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:uuid)\', CHANGE expires_at expires_at INT DEFAULT NULL, CHANGE scope scope VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
    }
}
