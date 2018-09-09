<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180908234805 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE map (id CHAR(36) NOT NULL --(DC2Type:guid)
        , width INTEGER NOT NULL, height INTEGER NOT NULL, player_count INTEGER NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE game (id CHAR(36) NOT NULL --(DC2Type:guid)
        , map_id CHAR(36) NOT NULL --(DC2Type:guid)
        , PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_232B318C53C55F64 ON game (map_id)');
        $this->addSql('CREATE TABLE player (id CHAR(36) NOT NULL --(DC2Type:guid)
        , game_id CHAR(36) NOT NULL --(DC2Type:guid)
        , player_number INTEGER NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_98197A65E48FD905 ON player (game_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP TABLE map');
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE player');
    }
}
