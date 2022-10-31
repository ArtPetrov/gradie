<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191108125052 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE content_page_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE content_page (id INT NOT NULL, name VARCHAR(128) NOT NULL, version INT DEFAULT 1 NOT NULL, content_header VARCHAR(255) DEFAULT NULL, content_body TEXT DEFAULT NULL, template VARCHAR(128) DEFAULT NULL, status INT DEFAULT 200 NOT NULL, slug VARCHAR(255) NOT NULL, seo_title VARCHAR(255) DEFAULT NULL, seo_description VARCHAR(255) DEFAULT NULL, seo_keywords VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D9685BE5989D9B62 ON content_page (slug)');
        $this->addSql('CREATE INDEX slug_idx ON content_page (slug)');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE content_page_id_seq CASCADE');
        $this->addSql('DROP TABLE content_page');
    }
}
