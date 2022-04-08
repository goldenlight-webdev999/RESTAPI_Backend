<?php declare(strict_types=1);

namespace App\Infrastructure\Database\Migrations1;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180726132724 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE stripe_customer_subscription (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', customer_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', subscription_id VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_FD825FD29395C3F3 (customer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stripe_customer (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', user_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', customer_key VARCHAR(255) NOT NULL, date_added DATETIME NOT NULL COMMENT \'(DC2Type:datetimetz_immutable)\', UNIQUE INDEX UNIQ_DC7E523AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE stripe_customer_subscription ADD CONSTRAINT FK_FD825FD29395C3F3 FOREIGN KEY (customer_id) REFERENCES stripe_customer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE stripe_customer ADD CONSTRAINT FK_DC7E523AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE stripe_customer_subscription DROP FOREIGN KEY FK_FD825FD29395C3F3');
        $this->addSql('DROP TABLE stripe_customer_subscription');
        $this->addSql('DROP TABLE stripe_customer');
    }
}
