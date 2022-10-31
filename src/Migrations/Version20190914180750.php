<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190914180750 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE mailer_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE mailer_files_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE mailer_mail_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE mailer_recipient_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE mailer (id INT NOT NULL, mail_id INT DEFAULT NULL, name VARCHAR(128) NOT NULL, type VARCHAR(16) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, procces_status VARCHAR(16) NOT NULL, sender_name VARCHAR(128) NOT NULL, sender_email VARCHAR(128) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_98E6E5C8776F01 ON mailer (mail_id)');
        $this->addSql('CREATE TABLE mailer_files (id INT NOT NULL, mail_id INT DEFAULT NULL, file_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_15E4DDC0C8776F01 ON mailer_files (mail_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_15E4DDC093CB796C ON mailer_files (file_id)');
        $this->addSql('CREATE TABLE mailer_mail (id INT NOT NULL, header VARCHAR(126) NOT NULL, content TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE mailer_recipient (id INT NOT NULL, mailer_id INT NOT NULL, email VARCHAR(128) NOT NULL, status VARCHAR(16) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_411ACB443F989867 ON mailer_recipient (mailer_id)');
        $this->addSql('ALTER TABLE mailer ADD CONSTRAINT FK_98E6E5C8776F01 FOREIGN KEY (mail_id) REFERENCES mailer_mail (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE mailer_files ADD CONSTRAINT FK_15E4DDC0C8776F01 FOREIGN KEY (mail_id) REFERENCES mailer_mail (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE mailer_files ADD CONSTRAINT FK_15E4DDC093CB796C FOREIGN KEY (file_id) REFERENCES file (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE mailer_recipient ADD CONSTRAINT FK_411ACB443F989867 FOREIGN KEY (mailer_id) REFERENCES mailer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE mailer_recipient DROP CONSTRAINT FK_411ACB443F989867');
        $this->addSql('ALTER TABLE mailer DROP CONSTRAINT FK_98E6E5C8776F01');
        $this->addSql('ALTER TABLE mailer_files DROP CONSTRAINT FK_15E4DDC0C8776F01');
        $this->addSql('DROP SEQUENCE mailer_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE mailer_files_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE mailer_mail_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE mailer_recipient_id_seq CASCADE');
        $this->addSql('DROP TABLE mailer');
        $this->addSql('DROP TABLE mailer_files');
        $this->addSql('DROP TABLE mailer_mail');
        $this->addSql('DROP TABLE mailer_recipient');
    }
}
