<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200510140422 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER INDEX ecommerce_product_category_category_id_index RENAME TO IDX_8D06C85112469DE2');
        $this->addSql('DROP INDEX uniq_ff46a7ec1f1f2a24');
        $this->addSql('DROP INDEX UNIQ_5DD0B57C93CB796C');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5DD0B57C93CB796C ON ecommerce_product_images (file_id)');
        $this->addSql('ALTER INDEX uniq_acb06ec4fdd3612b RENAME TO IDX_ACB06EC4FDD3612B');
        $this->addSql('ALTER INDEX uniq_2a7ffe1c4584665a RENAME TO IDX_2A7FFE1C4584665A');
        $this->addSql('ALTER INDEX idx_673e57fce29f4fca RENAME TO IDX_673E57FC249E6EA1');
        $this->addSql('ALTER INDEX idx_12166e96e29f4fca RENAME TO IDX_12166E96249E6EA1');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP INDEX uniq_5dd0b57c93cb796c');
        $this->addSql('CREATE INDEX uniq_5dd0b57c93cb796c ON ecommerce_product_images (file_id)');
        $this->addSql('ALTER INDEX idx_acb06ec4fdd3612b RENAME TO uniq_acb06ec4fdd3612b');
        $this->addSql('ALTER INDEX idx_8d06c85112469de2 RENAME TO ecommerce_product_category_category_id_index');
        $this->addSql('CREATE INDEX uniq_ff46a7ec1f1f2a24 ON ecommerce_product_composition (element_id)');
        $this->addSql('ALTER INDEX idx_673e57fc249e6ea1 RENAME TO idx_673e57fce29f4fca');
        $this->addSql('ALTER INDEX idx_12166e96249e6ea1 RENAME TO idx_12166e96e29f4fca');
        $this->addSql('ALTER INDEX idx_2a7ffe1c4584665a RENAME TO uniq_2a7ffe1c4584665a');
    }
}
