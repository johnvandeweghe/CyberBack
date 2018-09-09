<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180909024050 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE unit (id CHAR(36) NOT NULL --(DC2Type:guid)
        , game_id CHAR(36) NOT NULL --(DC2Type:guid)
        , attack INTEGER NOT NULL, defense INTEGER NOT NULL, health INTEGER NOT NULL, unit_type VARCHAR(255) NOT NULL, min_range INTEGER NOT NULL, max_range INTEGER NOT NULL, speed INTEGER NOT NULL, x_position INTEGER DEFAULT NULL, y_position INTEGER DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DCBB0C53E48FD905 ON unit (game_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__map AS SELECT id, player_count FROM map');
        $this->addSql('DROP TABLE map');
        $this->addSql('CREATE TABLE map (id CHAR(36) NOT NULL COLLATE BINARY --(DC2Type:guid)
        , player_count INTEGER NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO map (id, player_count) SELECT id, player_count FROM __temp__map');
        $this->addSql('DROP TABLE __temp__map');
        $this->addSql('DROP INDEX IDX_232B318C53C55F64');
        $this->addSql('CREATE TEMPORARY TABLE __temp__game AS SELECT id, map_id FROM game');
        $this->addSql('DROP TABLE game');
        $this->addSql('CREATE TABLE game (id CHAR(36) NOT NULL COLLATE BINARY --(DC2Type:guid)
        , map_id CHAR(36) NOT NULL COLLATE BINARY --(DC2Type:guid)
        , PRIMARY KEY(id), CONSTRAINT FK_232B318C53C55F64 FOREIGN KEY (map_id) REFERENCES map (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO game (id, map_id) SELECT id, map_id FROM __temp__game');
        $this->addSql('DROP TABLE __temp__game');
        $this->addSql('CREATE INDEX IDX_232B318C53C55F64 ON game (map_id)');
        $this->addSql('DROP INDEX IDX_98197A65E48FD905');
        $this->addSql('CREATE TEMPORARY TABLE __temp__player AS SELECT id, game_id, player_number FROM player');
        $this->addSql('DROP TABLE player');
        $this->addSql('CREATE TABLE player (id CHAR(36) NOT NULL COLLATE BINARY --(DC2Type:guid)
        , game_id CHAR(36) NOT NULL COLLATE BINARY --(DC2Type:guid)
        , player_number INTEGER NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_98197A65E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO player (id, game_id, player_number) SELECT id, game_id, player_number FROM __temp__player');
        $this->addSql('DROP TABLE __temp__player');
        $this->addSql('CREATE INDEX IDX_98197A65E48FD905 ON player (game_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP TABLE unit');
        $this->addSql('DROP INDEX IDX_232B318C53C55F64');
        $this->addSql('CREATE TEMPORARY TABLE __temp__game AS SELECT id, map_id FROM game');
        $this->addSql('DROP TABLE game');
        $this->addSql('CREATE TABLE game (id CHAR(36) NOT NULL --(DC2Type:guid)
        , map_id CHAR(36) NOT NULL --(DC2Type:guid)
        , PRIMARY KEY(id))');
        $this->addSql('INSERT INTO game (id, map_id) SELECT id, map_id FROM __temp__game');
        $this->addSql('DROP TABLE __temp__game');
        $this->addSql('CREATE INDEX IDX_232B318C53C55F64 ON game (map_id)');
        $this->addSql('ALTER TABLE map ADD COLUMN width INTEGER NOT NULL');
        $this->addSql('ALTER TABLE map ADD COLUMN height INTEGER NOT NULL');
        $this->addSql('DROP INDEX IDX_98197A65E48FD905');
        $this->addSql('CREATE TEMPORARY TABLE __temp__player AS SELECT id, game_id, player_number FROM player');
        $this->addSql('DROP TABLE player');
        $this->addSql('CREATE TABLE player (id CHAR(36) NOT NULL --(DC2Type:guid)
        , game_id CHAR(36) NOT NULL --(DC2Type:guid)
        , player_number INTEGER NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO player (id, game_id, player_number) SELECT id, game_id, player_number FROM __temp__player');
        $this->addSql('DROP TABLE __temp__player');
        $this->addSql('CREATE INDEX IDX_98197A65E48FD905 ON player (game_id)');
    }
}
