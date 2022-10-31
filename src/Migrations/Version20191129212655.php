<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191129212655 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE salon_moderation_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE salon_owners_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE salon_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE salon_moderation (id INT NOT NULL, salon_id INT NOT NULL, dealer_id INT NOT NULL, comment VARCHAR(255) DEFAULT NULL, version INT DEFAULT 1 NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, coord_lat VARCHAR(32) NOT NULL, coord_lon VARCHAR(32) NOT NULL, type VARCHAR(32) NOT NULL, status VARCHAR(255) NOT NULL, info_name VARCHAR(128) DEFAULT NULL, info_address VARCHAR(255) DEFAULT NULL, info_timetable VARCHAR(128) DEFAULT NULL, info_phone VARCHAR(128) DEFAULT NULL, info_email VARCHAR(128) DEFAULT NULL, info_site VARCHAR(128) DEFAULT NULL, info_comment VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_673E57FC4C91BDE4 ON salon_moderation (salon_id)');
        $this->addSql('CREATE INDEX IDX_673E57FCE29F4FCA ON salon_moderation (dealer_id)');
        $this->addSql('CREATE TABLE salon_owners (id INT NOT NULL, salon_id INT NOT NULL, dealer_id INT NOT NULL, version INT DEFAULT 1 NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_12166E964C91BDE4 ON salon_owners (salon_id)');
        $this->addSql('CREATE INDEX IDX_12166E96E29F4FCA ON salon_owners (dealer_id)');
        $this->addSql('CREATE TABLE salon (id INT NOT NULL, version INT DEFAULT 1 NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, coord_lat VARCHAR(32) NOT NULL, coord_lon VARCHAR(32) NOT NULL, type VARCHAR(32) NOT NULL, info_name VARCHAR(128) DEFAULT NULL, info_address VARCHAR(255) DEFAULT NULL, info_timetable VARCHAR(128) DEFAULT NULL, info_phone VARCHAR(128) DEFAULT NULL, info_email VARCHAR(128) DEFAULT NULL, info_site VARCHAR(128) DEFAULT NULL, info_comment VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE salon_moderation ADD CONSTRAINT FK_673E57FC4C91BDE4 FOREIGN KEY (salon_id) REFERENCES salon (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE salon_moderation ADD CONSTRAINT FK_673E57FCE29F4FCA FOREIGN KEY (dealer_id) REFERENCES dealer (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE salon_owners ADD CONSTRAINT FK_12166E964C91BDE4 FOREIGN KEY (salon_id) REFERENCES salon (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE salon_owners ADD CONSTRAINT FK_12166E96E29F4FCA FOREIGN KEY (dealer_id) REFERENCES dealer (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE salon_moderation DROP CONSTRAINT FK_673E57FC4C91BDE4');
        $this->addSql('ALTER TABLE salon_owners DROP CONSTRAINT FK_12166E964C91BDE4');
        $this->addSql('DROP SEQUENCE salon_moderation_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE salon_owners_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE salon_id_seq CASCADE');
        $this->addSql('DROP TABLE salon_moderation');
        $this->addSql('DROP TABLE salon_owners');
        $this->addSql('DROP TABLE salon');
    }
}
