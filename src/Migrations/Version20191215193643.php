<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191215193643 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE sliders_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE sliders (id INT NOT NULL, cover_id INT DEFAULT NULL, enable BOOLEAN NOT NULL, position INT NOT NULL, version INT DEFAULT 1 NOT NULL, slider_header VARCHAR(255) NOT NULL, slider_description VARCHAR(255) NOT NULL, button_enable BOOLEAN NOT NULL, button_label VARCHAR(255) DEFAULT NULL, button_link VARCHAR(255) DEFAULT NULL, type VARCHAR(12) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_85A59DB8922726E9 ON sliders (cover_id)');
        $this->addSql('CREATE INDEX sliders_enable ON sliders (type, enable)');
        $this->addSql('CREATE INDEX sliders_position ON sliders (type, position)');
        $this->addSql('ALTER TABLE sliders ADD CONSTRAINT FK_85A59DB8922726E9 FOREIGN KEY (cover_id) REFERENCES file (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE sliders_id_seq CASCADE');
        $this->addSql('DROP TABLE sliders');
    }
}
