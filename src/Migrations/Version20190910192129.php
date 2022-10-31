<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190910192129 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE dealer DROP CONSTRAINT FK_17A33902E3FEBC91');
        $this->addSql('ALTER TABLE dealer DROP CONSTRAINT FK_17A33902783E3463');
        $this->addSql('ALTER TABLE dealer ADD CONSTRAINT FK_17A33902E3FEBC91 FOREIGN KEY (category_dealer_id) REFERENCES category_dealer (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE dealer ADD CONSTRAINT FK_17A33902783E3463 FOREIGN KEY (manager_id) REFERENCES manager (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE dealer DROP CONSTRAINT fk_17a33902e3febc91');
        $this->addSql('ALTER TABLE dealer DROP CONSTRAINT fk_17a33902783e3463');
        $this->addSql('ALTER TABLE dealer ADD CONSTRAINT fk_17a33902e3febc91 FOREIGN KEY (category_dealer_id) REFERENCES category_dealer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE dealer ADD CONSTRAINT fk_17a33902783e3463 FOREIGN KEY (manager_id) REFERENCES manager (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
