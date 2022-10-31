<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200217031500 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE promocode_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE promocode (id INT NOT NULL, enable BOOLEAN DEFAULT \'true\' NOT NULL, value DOUBLE PRECISION NOT NULL, used INT DEFAULT 0 NOT NULL, version INT DEFAULT 1 NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, type_value VARCHAR(16) NOT NULL, code VARCHAR(128) NOT NULL, name VARCHAR(64) NOT NULL, description VARCHAR(255) DEFAULT NULL, restrictions_count_limit INT NOT NULL, restrictions_date_start DATE DEFAULT NULL, restrictions_date_end DATE DEFAULT NULL, restrictions_min_sum_order DOUBLE PRECISION NOT NULL, restrictions_max_sum_order DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7C786E0677153098 ON promocode (code)');
        $this->addSql('COMMENT ON COLUMN promocode.restrictions_date_start IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN promocode.restrictions_date_end IS \'(DC2Type:date_immutable)\'');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE promocode_id_seq CASCADE');
        $this->addSql('DROP INDEX UNIQ_7C786E0677153098');
        $this->addSql('DROP TABLE promocode');
    }
}
