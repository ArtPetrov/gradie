<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200516030346 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE ecommerce_product_group_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE ecommerce_product_group_link_on_product_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE ecommerce_product_group_selector_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE ecommerce_product_group (id INT NOT NULL, name VARCHAR(255) NOT NULL, version INT DEFAULT 1 NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX product_group_name ON ecommerce_product_group (name)');
        $this->addSql('CREATE TABLE ecommerce_product_group_link_on_product (id INT NOT NULL, group_id INT DEFAULT NULL, product_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_858843FBFE54D947 ON ecommerce_product_group_link_on_product (group_id)');
        $this->addSql('CREATE INDEX IDX_858843FB4584665A ON ecommerce_product_group_link_on_product (product_id)');
        $this->addSql('CREATE TABLE ecommerce_product_group_selector (id INT NOT NULL, group_id INT DEFAULT NULL, attribute_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, position INT DEFAULT 0 NOT NULL, version INT DEFAULT 1 NOT NULL, type_value VARCHAR(32) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2F98A2B0FE54D947 ON ecommerce_product_group_selector (group_id)');
        $this->addSql('CREATE INDEX IDX_2F98A2B0B6E62EFA ON ecommerce_product_group_selector (attribute_id)');
        $this->addSql('CREATE INDEX product_group_selector_position ON ecommerce_product_group_selector (position)');
        $this->addSql('ALTER TABLE ecommerce_product_group_link_on_product ADD CONSTRAINT FK_858843FBFE54D947 FOREIGN KEY (group_id) REFERENCES ecommerce_product_group (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ecommerce_product_group_link_on_product ADD CONSTRAINT FK_858843FB4584665A FOREIGN KEY (product_id) REFERENCES ecommerce_product (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ecommerce_product_group_selector ADD CONSTRAINT FK_2F98A2B0FE54D947 FOREIGN KEY (group_id) REFERENCES ecommerce_product_group (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ecommerce_product_group_selector ADD CONSTRAINT FK_2F98A2B0B6E62EFA FOREIGN KEY (attribute_id) REFERENCES ecommerce_attribute (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE ecommerce_product_group_link_on_product DROP CONSTRAINT FK_858843FBFE54D947');
        $this->addSql('ALTER TABLE ecommerce_product_group_selector DROP CONSTRAINT FK_2F98A2B0FE54D947');
        $this->addSql('DROP SEQUENCE ecommerce_product_group_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE ecommerce_product_group_link_on_product_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE ecommerce_product_group_selector_id_seq CASCADE');
        $this->addSql('DROP TABLE ecommerce_product_group');
        $this->addSql('DROP TABLE ecommerce_product_group_link_on_product');
        $this->addSql('DROP TABLE ecommerce_product_group_selector');
    }
}
