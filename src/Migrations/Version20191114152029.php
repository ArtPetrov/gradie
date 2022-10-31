<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191114152029 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE design_project_images_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE design_project_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE design_project_images (id INT NOT NULL, project_id INT NOT NULL, file_id INT NOT NULL, version INT DEFAULT 1 NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_46AF251893CB796C ON design_project_images (file_id)');
        $this->addSql('CREATE INDEX project_indx_dp ON design_project_images (project_id)');
        $this->addSql('CREATE TABLE design_project (id INT NOT NULL, sizes JSON DEFAULT NULL, comment TEXT DEFAULT NULL, version INT DEFAULT 1 NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, type VARCHAR(32) NOT NULL, client_name VARCHAR(64) DEFAULT NULL, client_phone VARCHAR(64) DEFAULT NULL, client_email VARCHAR(128) DEFAULT NULL, client_city VARCHAR(128) DEFAULT NULL, info_name VARCHAR(255) DEFAULT NULL, info_description TEXT DEFAULT NULL, status VARCHAR(32) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX date_project_idx ON design_project (created_at)');
        $this->addSql('COMMENT ON COLUMN design_project.sizes IS \'(DC2Type:design.project.size)\'');
        $this->addSql('ALTER TABLE design_project_images ADD CONSTRAINT FK_46AF2518166D1F9C FOREIGN KEY (project_id) REFERENCES design_project (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE design_project_images ADD CONSTRAINT FK_46AF251893CB796C FOREIGN KEY (file_id) REFERENCES file (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE design_project_images DROP CONSTRAINT FK_46AF2518166D1F9C');
        $this->addSql('DROP SEQUENCE design_project_images_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE design_project_id_seq CASCADE');
        $this->addSql('DROP TABLE design_project_images');
        $this->addSql('DROP TABLE design_project');
    }
}
