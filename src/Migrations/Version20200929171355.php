<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200929171355 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE quiz_quest_value ADD cover_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE quiz_quest_value ALTER value DROP NOT NULL');
        $this->addSql('ALTER TABLE quiz_quest_value ADD CONSTRAINT FK_61ECD208922726E9 FOREIGN KEY (cover_id) REFERENCES file (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_61ECD208922726E9 ON quiz_quest_value (cover_id)');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE quiz_quest_value DROP CONSTRAINT FK_61ECD208922726E9');
        $this->addSql('DROP INDEX UNIQ_61ECD208922726E9');
        $this->addSql('ALTER TABLE quiz_quest_value DROP cover_id');
        $this->addSql('ALTER TABLE quiz_quest_value ALTER value SET NOT NULL');
    }
}
