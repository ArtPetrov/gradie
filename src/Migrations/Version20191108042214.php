<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191108042214 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE ecommerce_product_category DROP CONSTRAINT FK_8D06C85112469DE2');
        $this->addSql('ALTER TABLE ecommerce_product_category ADD CONSTRAINT FK_8D06C85112469DE2 FOREIGN KEY (category_id) REFERENCES ecommerce_category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE ecommerce_product_category DROP CONSTRAINT fk_8d06c85112469de2');
        $this->addSql('ALTER TABLE ecommerce_product_category ADD CONSTRAINT fk_8d06c85112469de2 FOREIGN KEY (category_id) REFERENCES ecommerce_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
