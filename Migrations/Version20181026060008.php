<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181026060008 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE lobby_manager (name VARCHAR(255) NOT NULL, callback_url VARCHAR(255) NOT NULL, secret VARCHAR(255) NOT NULL, PRIMARY KEY(name))');
        $this->addSql('CREATE TEMPORARY TABLE __temp__map AS SELECT id, player_count FROM map');
        $this->addSql('DROP TABLE map');
        $this->addSql('CREATE TABLE map (id CHAR(36) NOT NULL COLLATE BINARY --(DC2Type:guid)
        , lobby_manager_name VARCHAR(255) DEFAULT NULL, player_count INTEGER NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_93ADAABB342BF20B FOREIGN KEY (lobby_manager_name) REFERENCES lobby_manager (name) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO map (id, player_count) SELECT id, player_count FROM __temp__map');
        $this->addSql('DROP TABLE __temp__map');
        $this->addSql('CREATE INDEX IDX_93ADAABB342BF20B ON map (lobby_manager_name)');
        $this->addSql('DROP INDEX IDX_232B318C53C55F64');
        $this->addSql('CREATE TEMPORARY TABLE __temp__game AS SELECT id, map_id FROM game');
        $this->addSql('DROP TABLE game');
        $this->addSql('CREATE TABLE game (id CHAR(36) NOT NULL COLLATE BINARY --(DC2Type:guid)
        , map_id CHAR(36) NOT NULL COLLATE BINARY --(DC2Type:guid)
        , PRIMARY KEY(id), CONSTRAINT FK_232B318C53C55F64 FOREIGN KEY (map_id) REFERENCES map (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO game (id, map_id) SELECT id, map_id FROM __temp__game');
        $this->addSql('DROP TABLE __temp__game');
        $this->addSql('CREATE INDEX IDX_232B318C53C55F64 ON game (map_id)');
        $this->addSql('DROP INDEX IDX_2020154799E6F5DF');
        $this->addSql('CREATE TEMPORARY TABLE __temp__turn AS SELECT id, player_id, status, start_timestamp FROM turn');
        $this->addSql('DROP TABLE turn');
        $this->addSql('CREATE TABLE turn (id CHAR(36) NOT NULL COLLATE BINARY --(DC2Type:guid)
        , player_id CHAR(36) NOT NULL COLLATE BINARY --(DC2Type:guid)
        , status VARCHAR(255) NOT NULL COLLATE BINARY, start_timestamp DATETIME NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_2020154799E6F5DF FOREIGN KEY (player_id) REFERENCES player (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO turn (id, player_id, status, start_timestamp) SELECT id, player_id, status, start_timestamp FROM __temp__turn');
        $this->addSql('DROP TABLE __temp__turn');
        $this->addSql('CREATE INDEX IDX_2020154799E6F5DF ON turn (player_id)');
        $this->addSql('DROP INDEX IDX_98197A65E48FD905');
        $this->addSql('CREATE TEMPORARY TABLE __temp__player AS SELECT id, game_id, player_number FROM player');
        $this->addSql('DROP TABLE player');
        $this->addSql('CREATE TABLE player (id CHAR(36) NOT NULL COLLATE BINARY --(DC2Type:guid)
        , game_id CHAR(36) NOT NULL COLLATE BINARY --(DC2Type:guid)
        , player_number INTEGER NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_98197A65E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO player (id, game_id, player_number) SELECT id, game_id, player_number FROM __temp__player');
        $this->addSql('DROP TABLE __temp__player');
        $this->addSql('CREATE INDEX IDX_98197A65E48FD905 ON player (game_id)');
        $this->addSql('DROP INDEX IDX_DCBB0C5399E6F5DF');
        $this->addSql('CREATE TEMPORARY TABLE __temp__unit AS SELECT id, player_id, attack, defense, health, unit_type, min_range, max_range, speed, max_action_points, current_action_points, action_point_regen_rate, x_position, y_position FROM unit');
        $this->addSql('DROP TABLE unit');
        $this->addSql('CREATE TABLE unit (id CHAR(36) NOT NULL COLLATE BINARY --(DC2Type:guid)
        , player_id CHAR(36) NOT NULL COLLATE BINARY --(DC2Type:guid)
        , attack INTEGER NOT NULL, defense INTEGER NOT NULL, health INTEGER NOT NULL, unit_type VARCHAR(255) NOT NULL COLLATE BINARY, min_range INTEGER NOT NULL, max_range INTEGER NOT NULL, speed INTEGER NOT NULL, max_action_points INTEGER NOT NULL, current_action_points INTEGER NOT NULL, action_point_regen_rate INTEGER NOT NULL, x_position INTEGER DEFAULT NULL, y_position INTEGER DEFAULT NULL, PRIMARY KEY(id), CONSTRAINT FK_DCBB0C5399E6F5DF FOREIGN KEY (player_id) REFERENCES player (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO unit (id, player_id, attack, defense, health, unit_type, min_range, max_range, speed, max_action_points, current_action_points, action_point_regen_rate, x_position, y_position) SELECT id, player_id, attack, defense, health, unit_type, min_range, max_range, speed, max_action_points, current_action_points, action_point_regen_rate, x_position, y_position FROM __temp__unit');
        $this->addSql('DROP TABLE __temp__unit');
        $this->addSql('CREATE INDEX IDX_DCBB0C5399E6F5DF ON unit (player_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP TABLE lobby_manager');
        $this->addSql('DROP INDEX IDX_232B318C53C55F64');
        $this->addSql('CREATE TEMPORARY TABLE __temp__game AS SELECT id, map_id FROM game');
        $this->addSql('DROP TABLE game');
        $this->addSql('CREATE TABLE game (id CHAR(36) NOT NULL --(DC2Type:guid)
        , map_id CHAR(36) NOT NULL --(DC2Type:guid)
        , PRIMARY KEY(id))');
        $this->addSql('INSERT INTO game (id, map_id) SELECT id, map_id FROM __temp__game');
        $this->addSql('DROP TABLE __temp__game');
        $this->addSql('CREATE INDEX IDX_232B318C53C55F64 ON game (map_id)');
        $this->addSql('DROP INDEX IDX_93ADAABB342BF20B');
        $this->addSql('CREATE TEMPORARY TABLE __temp__map AS SELECT id, player_count FROM map');
        $this->addSql('DROP TABLE map');
        $this->addSql('CREATE TABLE map (id CHAR(36) NOT NULL --(DC2Type:guid)
        , player_count INTEGER NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO map (id, player_count) SELECT id, player_count FROM __temp__map');
        $this->addSql('DROP TABLE __temp__map');
        $this->addSql('DROP INDEX IDX_98197A65E48FD905');
        $this->addSql('CREATE TEMPORARY TABLE __temp__player AS SELECT id, game_id, player_number FROM player');
        $this->addSql('DROP TABLE player');
        $this->addSql('CREATE TABLE player (id CHAR(36) NOT NULL --(DC2Type:guid)
        , game_id CHAR(36) NOT NULL --(DC2Type:guid)
        , player_number INTEGER NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO player (id, game_id, player_number) SELECT id, game_id, player_number FROM __temp__player');
        $this->addSql('DROP TABLE __temp__player');
        $this->addSql('CREATE INDEX IDX_98197A65E48FD905 ON player (game_id)');
        $this->addSql('DROP INDEX IDX_2020154799E6F5DF');
        $this->addSql('CREATE TEMPORARY TABLE __temp__turn AS SELECT id, player_id, status, start_timestamp FROM turn');
        $this->addSql('DROP TABLE turn');
        $this->addSql('CREATE TABLE turn (id CHAR(36) NOT NULL --(DC2Type:guid)
        , player_id CHAR(36) NOT NULL --(DC2Type:guid)
        , status VARCHAR(255) NOT NULL, start_timestamp DATETIME NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO turn (id, player_id, status, start_timestamp) SELECT id, player_id, status, start_timestamp FROM __temp__turn');
        $this->addSql('DROP TABLE __temp__turn');
        $this->addSql('CREATE INDEX IDX_2020154799E6F5DF ON turn (player_id)');
        $this->addSql('DROP INDEX IDX_DCBB0C5399E6F5DF');
        $this->addSql('CREATE TEMPORARY TABLE __temp__unit AS SELECT id, player_id, attack, defense, health, unit_type, min_range, max_range, speed, max_action_points, current_action_points, action_point_regen_rate, x_position, y_position FROM unit');
        $this->addSql('DROP TABLE unit');
        $this->addSql('CREATE TABLE unit (id CHAR(36) NOT NULL --(DC2Type:guid)
        , player_id CHAR(36) NOT NULL --(DC2Type:guid)
        , attack INTEGER NOT NULL, defense INTEGER NOT NULL, health INTEGER NOT NULL, unit_type VARCHAR(255) NOT NULL, min_range INTEGER NOT NULL, max_range INTEGER NOT NULL, speed INTEGER NOT NULL, max_action_points INTEGER NOT NULL, current_action_points INTEGER NOT NULL, action_point_regen_rate INTEGER NOT NULL, x_position INTEGER DEFAULT NULL, y_position INTEGER DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO unit (id, player_id, attack, defense, health, unit_type, min_range, max_range, speed, max_action_points, current_action_points, action_point_regen_rate, x_position, y_position) SELECT id, player_id, attack, defense, health, unit_type, min_range, max_range, speed, max_action_points, current_action_points, action_point_regen_rate, x_position, y_position FROM __temp__unit');
        $this->addSql('DROP TABLE __temp__unit');
        $this->addSql('CREATE INDEX IDX_DCBB0C5399E6F5DF ON unit (player_id)');
    }
}
