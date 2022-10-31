<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191112053414 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE content_news_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE content_news_images_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE content_news (id INT NOT NULL, published_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, content TEXT DEFAULT NULL, version INT DEFAULT 1 NOT NULL, name_short VARCHAR(255) NOT NULL, name_full VARCHAR(255) NOT NULL, seo_title VARCHAR(255) DEFAULT NULL, seo_description VARCHAR(255) DEFAULT NULL, seo_keywords VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX pat_article_idx ON content_news (published_at)');
        $this->addSql('COMMENT ON COLUMN content_news.published_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE content_news_images (id INT NOT NULL, news_id INT NOT NULL, file_id INT NOT NULL, cover BOOLEAN NOT NULL, position INT NOT NULL, version INT DEFAULT 1 NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3F9CD79893CB796C ON content_news_images (file_id)');
        $this->addSql('CREATE INDEX position_idx_photo_news ON content_news_images (position)');
        $this->addSql('CREATE INDEX news_idx_photo ON content_news_images (news_id)');
        $this->addSql('CREATE INDEX news_cover_idx_photo ON content_news_images (news_id, cover) WHERE cover');
        $this->addSql('ALTER TABLE content_news_images ADD CONSTRAINT FK_3F9CD798B5A459A0 FOREIGN KEY (news_id) REFERENCES content_news (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE content_news_images ADD CONSTRAINT FK_3F9CD79893CB796C FOREIGN KEY (file_id) REFERENCES file (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE content_news_images DROP CONSTRAINT FK_3F9CD798B5A459A0');
        $this->addSql('DROP SEQUENCE content_news_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE content_news_images_id_seq CASCADE');
        $this->addSql('DROP TABLE content_news');
        $this->addSql('DROP TABLE content_news_images');
    }
}
