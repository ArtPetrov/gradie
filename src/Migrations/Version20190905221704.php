<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190905221704 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE ticket_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE ticket_message_files_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE ticket_message_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE ticket (id INT NOT NULL, dealer_id INT NOT NULL, header VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, process_status VARCHAR(16) NOT NULL, process_state VARCHAR(32) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_97A0ADA3249E6EA1 ON ticket (dealer_id)');
        $this->addSql('CREATE INDEX IDX_97A0ADA3B1ECFBBB ON ticket (process_status)');
        $this->addSql('CREATE TABLE ticket_message_files (id INT NOT NULL, message_id INT NOT NULL, file_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B857132E537A1329 ON ticket_message_files (message_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B857132E93CB796C ON ticket_message_files (file_id)');
        $this->addSql('CREATE TABLE ticket_message (id INT NOT NULL, ticket_id INT NOT NULL, dealer_id INT DEFAULT NULL, support_id INT DEFAULT NULL, content TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, message_type VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_BA71692D700047D2 ON ticket_message (ticket_id)');
        $this->addSql('CREATE INDEX IDX_BA71692D249E6EA1 ON ticket_message (dealer_id)');
        $this->addSql('CREATE INDEX IDX_BA71692D315B405 ON ticket_message (support_id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3249E6EA1 FOREIGN KEY (dealer_id) REFERENCES dealer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ticket_message_files ADD CONSTRAINT FK_B857132E537A1329 FOREIGN KEY (message_id) REFERENCES ticket_message (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ticket_message_files ADD CONSTRAINT FK_B857132E93CB796C FOREIGN KEY (file_id) REFERENCES file (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ticket_message ADD CONSTRAINT FK_BA71692D700047D2 FOREIGN KEY (ticket_id) REFERENCES ticket (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ticket_message ADD CONSTRAINT FK_BA71692D249E6EA1 FOREIGN KEY (dealer_id) REFERENCES dealer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ticket_message ADD CONSTRAINT FK_BA71692D315B405 FOREIGN KEY (support_id) REFERENCES administrator (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE ticket_message DROP CONSTRAINT FK_BA71692D700047D2');
        $this->addSql('ALTER TABLE ticket_message_files DROP CONSTRAINT FK_B857132E537A1329');
        $this->addSql('DROP SEQUENCE ticket_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE ticket_message_files_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE ticket_message_id_seq CASCADE');
        $this->addSql('DROP TABLE ticket');
        $this->addSql('DROP TABLE ticket_message_files');
        $this->addSql('DROP TABLE ticket_message');
    }
}
