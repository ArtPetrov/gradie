<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191111174221 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE content_gallery_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE content_gallery_images_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE content_gallery (id INT NOT NULL, position INT NOT NULL, version INT DEFAULT 1 NOT NULL, name_short VARCHAR(255) NOT NULL, name_full VARCHAR(255) NOT NULL, seo_title VARCHAR(255) DEFAULT NULL, seo_description VARCHAR(255) DEFAULT NULL, seo_keywords VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX position_gallery_idx ON content_gallery (position)');
        $this->addSql('CREATE TABLE content_gallery_images (id INT NOT NULL, album_id INT NOT NULL, file_id INT NOT NULL, cover BOOLEAN NOT NULL, position INT NOT NULL, version INT DEFAULT 1 NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2877A81C93CB796C ON content_gallery_images (file_id)');
        $this->addSql('CREATE INDEX position_idx_photo ON content_gallery_images (position)');
        $this->addSql('CREATE INDEX album_idx_photo ON content_gallery_images (album_id)');
        $this->addSql('CREATE INDEX album_cover_idx_photo ON content_gallery_images (album_id, cover) WHERE cover');
        $this->addSql('ALTER TABLE content_gallery_images ADD CONSTRAINT FK_2877A81C1137ABCF FOREIGN KEY (album_id) REFERENCES content_gallery (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE content_gallery_images ADD CONSTRAINT FK_2877A81C93CB796C FOREIGN KEY (file_id) REFERENCES file (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE content_gallery_images DROP CONSTRAINT FK_2877A81C1137ABCF');
        $this->addSql('DROP SEQUENCE content_gallery_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE content_gallery_images_id_seq CASCADE');
        $this->addSql('DROP TABLE content_gallery');
        $this->addSql('DROP TABLE content_gallery_images');
    }
}
