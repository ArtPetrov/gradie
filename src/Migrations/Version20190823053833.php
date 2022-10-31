<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190823053833 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE dealer_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE dealer (id INT NOT NULL, category_dealer_id INT DEFAULT NULL, manager_id INT DEFAULT NULL, email VARCHAR(128) NOT NULL, password VARCHAR(128) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, information_name VARCHAR(128) DEFAULT NULL, information_site VARCHAR(255) DEFAULT NULL, information_phone VARCHAR(64) DEFAULT NULL, information_address VARCHAR(255) DEFAULT NULL, information_inn VARCHAR(12) DEFAULT NULL, information_kpp VARCHAR(12) DEFAULT NULL, reset_token VARCHAR(255) DEFAULT NULL, reset_expires TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, moderation_status VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_17A33902E7927C74 ON dealer (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_17A33902D7C8DC19 ON dealer (reset_token)');
        $this->addSql('CREATE INDEX IDX_17A33902E3FEBC91 ON dealer (category_dealer_id)');
        $this->addSql('CREATE INDEX IDX_17A33902783E3463 ON dealer (manager_id)');
        $this->addSql('COMMENT ON COLUMN dealer.reset_expires IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE dealer ADD CONSTRAINT FK_17A33902E3FEBC91 FOREIGN KEY (category_dealer_id) REFERENCES category_dealer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE dealer ADD CONSTRAINT FK_17A33902783E3463 FOREIGN KEY (manager_id) REFERENCES manager (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE dealer_id_seq CASCADE');
        $this->addSql('DROP TABLE dealer');
    }
}
