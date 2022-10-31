<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191112085115 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE content_works_composition_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE content_works_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE content_works_images_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE content_works_composition (id INT NOT NULL, work_id INT NOT NULL, product_id INT NOT NULL, count INT NOT NULL, position INT NOT NULL, version INT DEFAULT 1 NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2A7FFE1C4584665A ON content_works_composition (product_id)');
        $this->addSql('CREATE INDEX id_idx_work ON content_works_composition (work_id)');
        $this->addSql('CREATE INDEX position_idx_work ON content_works_composition (position)');
        $this->addSql('CREATE TABLE content_works (id INT NOT NULL, position INT NOT NULL, version INT DEFAULT 1 NOT NULL, name VARCHAR(255) NOT NULL, header VARCHAR(255) NOT NULL, content TEXT DEFAULT NULL, price VARCHAR(128) DEFAULT NULL, seo_title VARCHAR(255) DEFAULT NULL, seo_description VARCHAR(255) DEFAULT NULL, seo_keywords VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX work_pos_idx ON content_works (position)');
        $this->addSql('CREATE TABLE content_works_images (id INT NOT NULL, work_id INT NOT NULL, file_id INT NOT NULL, cover BOOLEAN NOT NULL, position INT NOT NULL, version INT DEFAULT 1 NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_66B69A6D93CB796C ON content_works_images (file_id)');
        $this->addSql('CREATE INDEX position_idx_photo_work ON content_works_images (position)');
        $this->addSql('CREATE INDEX work_idx_photo ON content_works_images (work_id)');
        $this->addSql('CREATE INDEX work_cover_idx ON content_works_images (work_id, cover) WHERE cover');
        $this->addSql('ALTER TABLE content_works_composition ADD CONSTRAINT FK_2A7FFE1CBB3453DB FOREIGN KEY (work_id) REFERENCES content_works (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE content_works_composition ADD CONSTRAINT FK_2A7FFE1C4584665A FOREIGN KEY (product_id) REFERENCES ecommerce_product (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE content_works_images ADD CONSTRAINT FK_66B69A6DBB3453DB FOREIGN KEY (work_id) REFERENCES content_works (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE content_works_images ADD CONSTRAINT FK_66B69A6D93CB796C FOREIGN KEY (file_id) REFERENCES file (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE content_works_composition DROP CONSTRAINT FK_2A7FFE1CBB3453DB');
        $this->addSql('ALTER TABLE content_works_images DROP CONSTRAINT FK_66B69A6DBB3453DB');
        $this->addSql('DROP SEQUENCE content_works_composition_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE content_works_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE content_works_images_id_seq CASCADE');
        $this->addSql('DROP TABLE content_works_composition');
        $this->addSql('DROP TABLE content_works');
        $this->addSql('DROP TABLE content_works_images');
    }
}
