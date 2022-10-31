<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200220134707 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE basket_items_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE basket_items (id INT NOT NULL, product_id INT NOT NULL, count INT DEFAULT 1 NOT NULL, version INT DEFAULT 1 NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, token UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B766A2774584665A ON basket_items (product_id)');
        $this->addSql('CREATE INDEX IDX_B766A2775F37A13B ON basket_items (token)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B766A2775F37A13B4584665A ON basket_items (token, product_id)');
        $this->addSql('ALTER TABLE basket_items ADD CONSTRAINT FK_B766A2774584665A FOREIGN KEY (product_id) REFERENCES ecommerce_product (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');

    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE basket_items_id_seq CASCADE');
        $this->addSql('DROP TABLE basket_items');
    }
}
