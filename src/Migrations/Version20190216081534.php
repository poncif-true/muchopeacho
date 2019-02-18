<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190216081534 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE peacher ADD display_username VARCHAR(180) DEFAULT NULL, CHANGE username username VARCHAR(255) NOT NULL, CHANGE style style VARCHAR(180) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_44B982C69FB4A359 ON peacher (display_username)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_44B982C69FB4A359 ON peacher');
        $this->addSql('ALTER TABLE peacher DROP display_username, CHANGE username username VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE style style VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
    }
}
