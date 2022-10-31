<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200927001027 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE quiz_quest_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE quiz_quest_value_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE quiz_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE quiz_quest (id INT NOT NULL, skip BOOLEAN NOT NULL, name VARCHAR(255) NOT NULL, quest VARCHAR(255) DEFAULT NULL, help VARCHAR(255) DEFAULT NULL, another_answer BOOLEAN NOT NULL, version INT DEFAULT 1 NOT NULL, type VARCHAR(32) NOT NULL, Quiz INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_BF64819742055AC ON quiz_quest (Quiz)');
        $this->addSql('CREATE TABLE quiz_quest_value (id INT NOT NULL, title VARCHAR(255) DEFAULT NULL, value VARCHAR(255) NOT NULL, is_media BOOLEAN NOT NULL, style VARCHAR(255) DEFAULT NULL, version INT DEFAULT 1 NOT NULL, Quest INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_61ECD20882D6D713 ON quiz_quest_value (Quest)');
        $this->addSql('CREATE TABLE quiz (id INT NOT NULL, enable BOOLEAN NOT NULL, name VARCHAR(255) NOT NULL, text_begin VARCHAR(255) NOT NULL, text_end VARCHAR(255) NOT NULL, content TEXT DEFAULT NULL, map JSON DEFAULT NULL, version INT DEFAULT 1 NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE quiz_quest ADD CONSTRAINT FK_BF64819742055AC FOREIGN KEY (Quiz) REFERENCES quiz (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quiz_quest_value ADD CONSTRAINT FK_61ECD20882D6D713 FOREIGN KEY (Quest) REFERENCES quiz_quest (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE quiz_quest_value DROP CONSTRAINT FK_61ECD20882D6D713');
        $this->addSql('ALTER TABLE quiz_quest DROP CONSTRAINT FK_BF64819742055AC');
        $this->addSql('DROP SEQUENCE quiz_quest_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE quiz_quest_value_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE quiz_id_seq CASCADE');
        $this->addSql('DROP TABLE quiz_quest');
        $this->addSql('DROP TABLE quiz_quest_value');
        $this->addSql('DROP TABLE quiz');
    }
}
