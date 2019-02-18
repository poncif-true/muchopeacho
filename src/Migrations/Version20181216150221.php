<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181216150221 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE movie_based_username (id INT AUTO_INCREMENT NOT NULL, tmdb_movie_id INT NOT NULL, tmdb_cast_id INT DEFAULT NULL, peacher_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, custom TINYINT(1) NOT NULL, insert_date DATETIME NOT NULL, update_date DATETIME NOT NULL, INDEX IDX_D6C73F7DB181EBB3 (tmdb_movie_id), INDEX IDX_D6C73F7D5B9F70E8 (tmdb_cast_id), UNIQUE INDEX UNIQ_D6C73F7D486BDE67 (peacher_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE movie_based_username ADD CONSTRAINT FK_D6C73F7DB181EBB3 FOREIGN KEY (tmdb_movie_id) REFERENCES tmdb_movie (id)');
        $this->addSql('ALTER TABLE movie_based_username ADD CONSTRAINT FK_D6C73F7D5B9F70E8 FOREIGN KEY (tmdb_cast_id) REFERENCES tmdb_cast (id)');
        $this->addSql('ALTER TABLE movie_based_username ADD CONSTRAINT FK_D6C73F7D486BDE67 FOREIGN KEY (peacher_id) REFERENCES peacher (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE movie_based_username');
    }
}
