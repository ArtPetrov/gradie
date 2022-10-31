<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191104071149 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE ecommerce_product_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE ecommerce_product_recommended_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE ecommerce_product_category_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE ecommerce_product_composition_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE ecommerce_product_images_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE ecommerce_product (id INT NOT NULL, enable BOOLEAN DEFAULT \'true\' NOT NULL, attributes JSONB DEFAULT NULL, version INT DEFAULT 1 NOT NULL, info_name VARCHAR(255) NOT NULL, info_article VARCHAR(128) NOT NULL, info_weight NUMERIC(10, 3) NOT NULL, info_weight_is_final BOOLEAN NOT NULL, info_volume NUMERIC(10, 3) NOT NULL, info_volume_is_final BOOLEAN NOT NULL, info_content TEXT DEFAULT NULL, price_current NUMERIC(10, 2) NOT NULL, price_old NUMERIC(10, 2) DEFAULT NULL, price_final BOOLEAN NOT NULL, seo_title VARCHAR(255) DEFAULT NULL, seo_description VARCHAR(255) DEFAULT NULL, seo_keywords VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C2D8016D80FF27F8 ON ecommerce_product (info_article)');
        $this->addSql('CREATE INDEX enable_idx ON ecommerce_product (enable) WHERE (enable = true)');
        $this->addSql('CREATE INDEX serach_for_kit_idx ON ecommerce_product (info_name, info_article, price_final) WHERE (price_final = true)');
        $this->addSql('CREATE INDEX article_idx ON ecommerce_product (info_article)');
        $this->addSql('CREATE INDEX attributes_idx ON ecommerce_product USING GIN(attributes)');
        $this->addSql('COMMENT ON COLUMN ecommerce_product.attributes IS \'(DC2Type:ecommerce.product.attributes)\'');
        $this->addSql('CREATE TABLE ecommerce_product_recommended (id INT NOT NULL, product_id INT NOT NULL, recommened_id INT NOT NULL, position INT NOT NULL, version INT DEFAULT 1 NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX UNIQ_ACB06EC4FDD3612B ON ecommerce_product_recommended (recommened_id)');
        $this->addSql('CREATE INDEX position_idx_rmd ON ecommerce_product_recommended (position)');
        $this->addSql('CREATE INDEX product_idx_rmd ON ecommerce_product_recommended (product_id)');
        $this->addSql('CREATE TABLE ecommerce_product_category (id INT NOT NULL, product_id INT NOT NULL, category_id INT NOT NULL, main BOOLEAN NOT NULL, version INT DEFAULT 1 NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX UNIQ_8D06C85112469DE2 ON ecommerce_product_category (category_id)');
        $this->addSql('CREATE INDEX product_idx_cat ON ecommerce_product_category (product_id)');
        $this->addSql('CREATE INDEX product_main_idx_cat ON ecommerce_product_category (product_id, main)');
        $this->addSql('CREATE TABLE ecommerce_product_composition (id INT NOT NULL, product_id INT NOT NULL, element_id INT NOT NULL, count INT NOT NULL, position INT NOT NULL, version INT DEFAULT 1 NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX UNIQ_FF46A7EC1F1F2A24 ON ecommerce_product_composition (element_id)');
        $this->addSql('CREATE INDEX element_idx_cp ON ecommerce_product_composition (element_id)');
        $this->addSql('CREATE INDEX product_idx_cp ON ecommerce_product_composition (product_id)');
        $this->addSql('CREATE INDEX position_idx_cp ON ecommerce_product_composition (position)');
        $this->addSql('CREATE TABLE ecommerce_product_images (id INT NOT NULL, product_id INT NOT NULL, file_id INT NOT NULL, cover BOOLEAN NOT NULL, position INT NOT NULL, version INT DEFAULT 1 NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5DD0B57C93CB796C ON ecommerce_product_images (file_id)');
        $this->addSql('CREATE INDEX position_idx_img ON ecommerce_product_images (position)');
        $this->addSql('CREATE INDEX product_idx_img ON ecommerce_product_images (product_id)');
        $this->addSql('CREATE INDEX product_cover_idx_img ON ecommerce_product_images (product_id, cover)');
        $this->addSql('ALTER TABLE ecommerce_product_recommended ADD CONSTRAINT FK_ACB06EC44584665A FOREIGN KEY (product_id) REFERENCES ecommerce_product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ecommerce_product_recommended ADD CONSTRAINT FK_ACB06EC4FDD3612B FOREIGN KEY (recommened_id) REFERENCES ecommerce_product (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ecommerce_product_category ADD CONSTRAINT FK_8D06C8514584665A FOREIGN KEY (product_id) REFERENCES ecommerce_product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ecommerce_product_category ADD CONSTRAINT FK_8D06C85112469DE2 FOREIGN KEY (category_id) REFERENCES ecommerce_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ecommerce_product_composition ADD CONSTRAINT FK_FF46A7EC4584665A FOREIGN KEY (product_id) REFERENCES ecommerce_product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ecommerce_product_composition ADD CONSTRAINT FK_FF46A7EC1F1F2A24 FOREIGN KEY (element_id) REFERENCES ecommerce_product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ecommerce_product_images ADD CONSTRAINT FK_5DD0B57C4584665A FOREIGN KEY (product_id) REFERENCES ecommerce_product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ecommerce_product_images ADD CONSTRAINT FK_5DD0B57C93CB796C FOREIGN KEY (file_id) REFERENCES file (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX search_fts_idx ON ecommerce_product USING GIN((setweight(to_tsvector(\'russian\', coalesce(info_name,\'\')), \'A\') || setweight(to_tsvector(\'russian\', coalesce(info_content,\'\')), \'B\')))');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE ecommerce_product_recommended DROP CONSTRAINT FK_ACB06EC44584665A');
        $this->addSql('ALTER TABLE ecommerce_product_recommended DROP CONSTRAINT FK_ACB06EC4FDD3612B');
        $this->addSql('ALTER TABLE ecommerce_product_category DROP CONSTRAINT FK_8D06C8514584665A');
        $this->addSql('ALTER TABLE ecommerce_product_composition DROP CONSTRAINT FK_FF46A7EC4584665A');
        $this->addSql('ALTER TABLE ecommerce_product_composition DROP CONSTRAINT FK_FF46A7EC1F1F2A24');
        $this->addSql('ALTER TABLE ecommerce_product_images DROP CONSTRAINT FK_5DD0B57C4584665A');
        $this->addSql('DROP SEQUENCE ecommerce_product_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE ecommerce_product_recommended_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE ecommerce_product_category_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE ecommerce_product_composition_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE ecommerce_product_images_id_seq CASCADE');
        $this->addSql('DROP TABLE ecommerce_product');
        $this->addSql('DROP TABLE ecommerce_product_recommended');
        $this->addSql('DROP TABLE ecommerce_product_category');
        $this->addSql('DROP TABLE ecommerce_product_composition');
        $this->addSql('DROP TABLE ecommerce_product_images');
    }
}
