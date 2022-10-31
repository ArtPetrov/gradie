<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200215022859 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE order_quick_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE order_quick (id INT NOT NULL, version INT DEFAULT 1 NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, client_name VARCHAR(64) NOT NULL, client_contact VARCHAR(128) NOT NULL, manager_comment VARCHAR(255) DEFAULT NULL, product_id INT NOT NULL, product_article VARCHAR(64) NOT NULL, product_name VARCHAR(255) NOT NULL, product_count INT NOT NULL, status VARCHAR(16) NOT NULL, PRIMARY KEY(id))');

    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE order_quick_id_seq CASCADE');
        $this->addSql('DROP TABLE order_quick');
    }
}
