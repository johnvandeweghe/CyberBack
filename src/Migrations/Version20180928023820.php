<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180928023820 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
	$this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');
        $this->addSql('INSERT INTO map (id, player_count) VALUES ("AE7E7566-5105-47DD-8438-3BEF9524A1AC", 2);');
        // this up() migration is auto-generated, please modify it to your needs

    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DELETE FROM map where id = "AE7E7566-5105-47DD-8438-3BEF9524A1AC";');

    }
}
