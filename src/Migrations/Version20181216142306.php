<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181216142306 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE username (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tmdb_cast (id INT AUTO_INCREMENT NOT NULL, tmdb_api_id INT NOT NULL, insert_date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, update_date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tmdb_movie (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, tmdb_api_id INT NOT NULL, realease_year INT DEFAULT NULL, insert_date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, update_date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, director_name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE username');
        $this->addSql('DROP TABLE tmdb_cast');
        $this->addSql('DROP TABLE tmdb_movie');
    }
}
