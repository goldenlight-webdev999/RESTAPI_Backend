<?php declare(strict_types=1);

namespace App\Infrastructure\Database\Migrations1;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180710125927 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE log_clean_task (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', user_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', o_auth_client_id INT DEFAULT NULL, file_name VARCHAR(255) NOT NULL, final_size INT DEFAULT NULL, original_size INT NOT NULL, client_ip VARCHAR(255) NOT NULL, user_agent VARCHAR(255) NOT NULL, execution_time INT NOT NULL, date_added DATETIME NOT NULL COMMENT \'(DC2Type:datetimetz_immutable)\', INDEX IDX_2756D289A76ED395 (user_id), INDEX IDX_2756D2894DAE4A33 (o_auth_client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE log_clean_task ADD CONSTRAINT FK_2756D289A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE log_clean_task ADD CONSTRAINT FK_2756D2894DAE4A33 FOREIGN KEY (o_auth_client_id) REFERENCES oauth2_client (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE log_clean_task');
    }
}
