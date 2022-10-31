<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191113114146 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE dealer ADD information_contrahens VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE dealer ADD request_company VARCHAR(128) DEFAULT NULL');
        $this->addSql('ALTER TABLE dealer ADD request_city VARCHAR(128) DEFAULT NULL');
        $this->addSql('ALTER TABLE dealer ADD request_leader VARCHAR(128) DEFAULT NULL');
        $this->addSql('ALTER TABLE dealer ADD request_profile TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE dealer ADD request_why_we TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE dealer ADD request_how_know TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE dealer ADD request_experience TEXT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE dealer DROP information_contrahens');
        $this->addSql('ALTER TABLE dealer DROP request_company');
        $this->addSql('ALTER TABLE dealer DROP request_city');
        $this->addSql('ALTER TABLE dealer DROP request_leader');
        $this->addSql('ALTER TABLE dealer DROP request_profile');
        $this->addSql('ALTER TABLE dealer DROP request_why_we');
        $this->addSql('ALTER TABLE dealer DROP request_how_know');
        $this->addSql('ALTER TABLE dealer DROP request_experience');
    }
}
