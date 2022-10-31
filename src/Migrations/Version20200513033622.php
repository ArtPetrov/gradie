<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200513033622 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE payment_invoice_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE payment_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE payment_invoice (id INT NOT NULL, payment INT NOT NULL, invoice UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_892C19AE6D28840D ON payment_invoice (payment)');
        $this->addSql('CREATE INDEX IDX_892C19AE90651744 ON payment_invoice (invoice)');
        $this->addSql('CREATE TABLE payment (id INT NOT NULL, uuid UUID NOT NULL, sum NUMERIC(10, 2) DEFAULT \'0\' NOT NULL, details TEXT DEFAULT NULL, version INT DEFAULT 1 NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, status_value VARCHAR(32) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6D28840DD17F50A6 ON payment (uuid)');
        $this->addSql('ALTER TABLE payment_invoice ADD CONSTRAINT FK_892C19AE6D28840D FOREIGN KEY (payment) REFERENCES payment (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payment_invoice ADD CONSTRAINT FK_892C19AE90651744 FOREIGN KEY (invoice) REFERENCES order_invoice (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE payment_invoice DROP CONSTRAINT FK_892C19AE6D28840D');
        $this->addSql('DROP SEQUENCE payment_invoice_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE payment_id_seq CASCADE');
        $this->addSql('DROP TABLE payment_invoice');
        $this->addSql('DROP TABLE payment');
    }
}
