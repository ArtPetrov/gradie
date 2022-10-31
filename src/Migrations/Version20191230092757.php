<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191230092757 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE content_works_images_diy_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE content_works_images_diy (id INT NOT NULL, work_id INT NOT NULL, file_id INT NOT NULL, position INT NOT NULL, version INT DEFAULT 1 NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4F9465C993CB796C ON content_works_images_diy (file_id)');
        $this->addSql('CREATE INDEX position_idx_photo_work_diy ON content_works_images_diy (position)');
        $this->addSql('CREATE INDEX work_idx_photo_diy ON content_works_images_diy (work_id)');
        $this->addSql('ALTER TABLE content_works_images_diy ADD CONSTRAINT FK_4F9465C9BB3453DB FOREIGN KEY (work_id) REFERENCES content_works (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE content_works_images_diy ADD CONSTRAINT FK_4F9465C993CB796C FOREIGN KEY (file_id) REFERENCES file (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE content_works_images_diy_id_seq CASCADE');
        $this->addSql('DROP TABLE content_works_images_diy');
    }
}
