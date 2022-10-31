<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200330043359 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');
        $this->addSql('CREATE SEQUENCE order_seq INCREMENT BY 1 MINVALUE 1000 START 1000');
        $this->addSql('CREATE TABLE orders (id INT NOT NULL, uuid UUID NOT NULL, manager_help BOOLEAN NOT NULL, version INT DEFAULT 1 NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, basket_token UUID NOT NULL, status VARCHAR(32) NOT NULL, client_name VARCHAR(64) DEFAULT NULL, client_phone VARCHAR(64) DEFAULT NULL, client_email VARCHAR(128) DEFAULT NULL, address_type VARCHAR(64) DEFAULT NULL, address_city VARCHAR(64) DEFAULT NULL, address_fields JSONB DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX uuid_idx ON orders (uuid)');
        $this->addSql('CREATE INDEX basket_token_idx ON orders (basket_token)');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');
        $this->addSql('DROP SEQUENCE order_seq CASCADE');
        $this->addSql('DROP TABLE orders');
    }
}
