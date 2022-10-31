<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191027215618 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE ecommerce_category_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE ecommerce_category (id INT NOT NULL, parent_id INT DEFAULT NULL, name VARCHAR(128) NOT NULL, slug VARCHAR(128) NOT NULL, path VARCHAR(255) DEFAULT NULL, level INT DEFAULT NULL, filters JSONB DEFAULT NULL, position INT NOT NULL, version INT DEFAULT 1 NOT NULL, seo_title VARCHAR(255) DEFAULT NULL, seo_description VARCHAR(255) DEFAULT NULL, seo_keywords VARCHAR(255) DEFAULT NULL, seo_content TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9D394974989D9B62 ON ecommerce_category (slug)');
        $this->addSql('CREATE INDEX IDX_9D394974727ACA70 ON ecommerce_category (parent_id)');
        $this->addSql('CREATE INDEX path_idx ON ecommerce_category (path)');
        $this->addSql('CREATE INDEX position_idx ON ecommerce_category (position)');
        $this->addSql('CREATE INDEX filters_idx ON ecommerce_category USING GIN (filters)');
        $this->addSql('COMMENT ON COLUMN ecommerce_category.filters IS \'(DC2Type:ecommerce.category.filters)\'');
        $this->addSql('ALTER TABLE ecommerce_category ADD CONSTRAINT FK_9D394974727ACA70 FOREIGN KEY (parent_id) REFERENCES ecommerce_category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE ecommerce_category DROP CONSTRAINT FK_9D394974727ACA70');
        $this->addSql('DROP SEQUENCE ecommerce_category_id_seq CASCADE');
        $this->addSql('DROP TABLE ecommerce_category');
    }
}
