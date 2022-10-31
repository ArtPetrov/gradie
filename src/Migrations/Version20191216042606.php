<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191216042606 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE popular_products_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE popular_products (id INT NOT NULL, cover_id INT DEFAULT NULL, header VARCHAR(255) NOT NULL, price VARCHAR(12) NOT NULL, link VARCHAR(255) NOT NULL, position INT NOT NULL, version INT DEFAULT 1 NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5D1234A0922726E9 ON popular_products (cover_id)');
        $this->addSql('CREATE INDEX popular_products_position ON popular_products (position)');
        $this->addSql('ALTER TABLE popular_products ADD CONSTRAINT FK_5D1234A0922726E9 FOREIGN KEY (cover_id) REFERENCES file (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE popular_products_id_seq CASCADE');
        $this->addSql('DROP TABLE popular_products');

    }
}
