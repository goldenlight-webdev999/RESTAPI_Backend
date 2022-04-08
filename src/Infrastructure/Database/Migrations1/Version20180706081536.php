<?php declare(strict_types=1);

namespace App\Infrastructure\Database\Migrations1;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180706081536 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql(
            'CREATE TABLE `sessions` (
                    `sess_id` VARCHAR(128) NOT NULL PRIMARY KEY,
                    `sess_data` BLOB NOT NULL,
                    `sess_time` INTEGER UNSIGNED NOT NULL,
                    `sess_lifetime` MEDIUMINT NOT NULL
                  ) COLLATE utf8_bin, ENGINE = InnoDB;'
        );

    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
