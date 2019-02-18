<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181216112949 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE movie_search ADD peacher_id INT NOT NULL');
        $this->addSql('ALTER TABLE movie_search ADD CONSTRAINT FK_4D3746D2486BDE67 FOREIGN KEY (peacher_id) REFERENCES peacher (id)');
        $this->addSql('CREATE INDEX IDX_4D3746D2486BDE67 ON movie_search (peacher_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE movie_search DROP FOREIGN KEY FK_4D3746D2486BDE67');
        $this->addSql('DROP INDEX IDX_4D3746D2486BDE67 ON movie_search');
        $this->addSql('ALTER TABLE movie_search DROP peacher_id');
    }
}
