<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190406110022 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE movie_based_username DROP FOREIGN KEY FK_D6C73F7D5B9F70E8');
        $this->addSql('ALTER TABLE movie_based_username DROP FOREIGN KEY FK_D6C73F7DB181EBB3');
        $this->addSql('CREATE TABLE avatar (id INT AUTO_INCREMENT NOT NULL, peacher_id INT NOT NULL, path VARCHAR(255) NOT NULL, insert_date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, update_date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, UNIQUE INDEX UNIQ_1677722F486BDE67 (peacher_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE avatar ADD CONSTRAINT FK_1677722F486BDE67 FOREIGN KEY (peacher_id) REFERENCES peacher (id)');
        $this->addSql('DROP TABLE movie_based_username');
        $this->addSql('DROP TABLE tmdb_cast');
        $this->addSql('DROP TABLE tmdb_movie');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE movie_based_username (id INT AUTO_INCREMENT NOT NULL, tmdb_movie_id INT NOT NULL, tmdb_cast_id INT DEFAULT NULL, peacher_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, custom TINYINT(1) NOT NULL, insert_date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, update_date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, INDEX IDX_D6C73F7DB181EBB3 (tmdb_movie_id), UNIQUE INDEX UNIQ_D6C73F7D486BDE67 (peacher_id), INDEX IDX_D6C73F7D5B9F70E8 (tmdb_cast_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE tmdb_cast (id INT AUTO_INCREMENT NOT NULL, tmdb_api_id INT NOT NULL, insert_date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, update_date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE tmdb_movie (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, tmdb_api_id INT NOT NULL, realease_year INT DEFAULT NULL, insert_date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, update_date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, director_name VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE movie_based_username ADD CONSTRAINT FK_D6C73F7D486BDE67 FOREIGN KEY (peacher_id) REFERENCES peacher (id)');
        $this->addSql('ALTER TABLE movie_based_username ADD CONSTRAINT FK_D6C73F7D5B9F70E8 FOREIGN KEY (tmdb_cast_id) REFERENCES tmdb_cast (id)');
        $this->addSql('ALTER TABLE movie_based_username ADD CONSTRAINT FK_D6C73F7DB181EBB3 FOREIGN KEY (tmdb_movie_id) REFERENCES tmdb_movie (id)');
        $this->addSql('DROP TABLE avatar');
    }
}
