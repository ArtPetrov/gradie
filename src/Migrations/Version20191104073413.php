<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191104073413 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE ecommerce_product_composition DROP CONSTRAINT FK_FF46A7EC1F1F2A24');
        $this->addSql('ALTER TABLE ecommerce_product_composition ADD CONSTRAINT FK_FF46A7EC1F1F2A24 FOREIGN KEY (element_id) REFERENCES ecommerce_product (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE ecommerce_product_composition DROP CONSTRAINT fk_ff46a7ec1f1f2a24');
        $this->addSql('ALTER TABLE ecommerce_product_composition ADD CONSTRAINT fk_ff46a7ec1f1f2a24 FOREIGN KEY (element_id) REFERENCES ecommerce_product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
