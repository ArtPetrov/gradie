<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200126182449 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE buyer_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE buyer_networks_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE buyer (id INT NOT NULL, version INT DEFAULT 1 NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, info_email VARCHAR(255) DEFAULT NULL, info_password VARCHAR(128) DEFAULT NULL, info_name VARCHAR(255) DEFAULT NULL, info_phone VARCHAR(64) DEFAULT NULL, reset_token VARCHAR(255) DEFAULT NULL, reset_expires TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_84905FB3D7C8DC19 ON buyer (reset_token)');
        $this->addSql('COMMENT ON COLUMN buyer.reset_expires IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE buyer_networks (id INT NOT NULL, buyer_id INT NOT NULL, network VARCHAR(32) DEFAULT NULL, identity VARCHAR(32) DEFAULT NULL, version INT DEFAULT 1 NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_607FCC8B6C755722 ON buyer_networks (buyer_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_607FCC8B608487BC6A95E9C4 ON buyer_networks (network, identity)');
        $this->addSql('ALTER TABLE buyer_networks ADD CONSTRAINT FK_607FCC8B6C755722 FOREIGN KEY (buyer_id) REFERENCES buyer (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');

    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE buyer_networks DROP CONSTRAINT FK_607FCC8B6C755722');
        $this->addSql('DROP SEQUENCE buyer_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE buyer_networks_id_seq CASCADE');
        $this->addSql('DROP TABLE buyer');
        $this->addSql('DROP TABLE buyer_networks');
    }
}
