<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200419184044 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE ecommerce_product_category ALTER main SET DEFAULT \'false\'');
        $this->addSql('ALTER TABLE ecommerce_product_composition ALTER count SET DEFAULT 1');
        $this->addSql('ALTER TABLE ecommerce_product_composition ALTER "position" SET DEFAULT 0');
        $this->addSql('ALTER TABLE ecommerce_product_images ALTER cover SET DEFAULT \'false\'');
        $this->addSql('ALTER TABLE ecommerce_product_images ALTER "position" SET DEFAULT 0');
        $this->addSql('ALTER TABLE ecommerce_product ALTER info_weight SET DEFAULT \'0\'');
        $this->addSql('ALTER TABLE ecommerce_product ALTER info_weight_is_final SET DEFAULT \'true\'');
        $this->addSql('ALTER TABLE ecommerce_product ALTER info_volume SET DEFAULT \'0\'');
        $this->addSql('ALTER TABLE ecommerce_product ALTER info_volume_is_final SET DEFAULT \'true\'');
        $this->addSql('ALTER TABLE ecommerce_product ALTER price_current SET DEFAULT \'0\'');
        $this->addSql('ALTER TABLE ecommerce_product ALTER price_old SET DEFAULT \'0\'');
        $this->addSql('ALTER TABLE ecommerce_product_recommended ALTER "position" SET DEFAULT 0');
        $this->addSql('ALTER TABLE content_gallery_images ALTER cover SET DEFAULT \'false\'');
        $this->addSql('ALTER TABLE content_gallery_images ALTER "position" SET DEFAULT 0');
        $this->addSql('ALTER TABLE content_news_images ALTER cover SET DEFAULT \'false\'');
        $this->addSql('ALTER TABLE content_news_images ALTER "position" SET DEFAULT 0');
        $this->addSql('ALTER TABLE content_works_composition ALTER count SET DEFAULT 1');
        $this->addSql('ALTER TABLE content_works_composition ALTER "position" SET DEFAULT 0');
        $this->addSql('ALTER TABLE content_works_images ALTER cover SET DEFAULT \'false\'');
        $this->addSql('ALTER TABLE content_works_images ALTER "position" SET DEFAULT 0');
        $this->addSql('ALTER TABLE content_works_images_diy ALTER "position" SET DEFAULT 0');
        $this->addSql('ALTER TABLE sliders ALTER enable SET DEFAULT \'true\'');
        $this->addSql('ALTER TABLE sliders ALTER button_enable SET DEFAULT \'false\'');
        $this->addSql('ALTER TABLE order_invoice ALTER sum SET DEFAULT \'0\'');
        $this->addSql('ALTER TABLE order_product ALTER count SET DEFAULT 1');
        $this->addSql('ALTER TABLE order_product ALTER price SET DEFAULT \'0\'');
        $this->addSql('ALTER TABLE order_product ALTER volume SET DEFAULT \'0\'');
        $this->addSql('ALTER TABLE order_product ALTER weight SET DEFAULT \'0\'');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE content_news_images ALTER cover DROP DEFAULT');
        $this->addSql('ALTER TABLE content_news_images ALTER position DROP DEFAULT');
        $this->addSql('ALTER TABLE content_works_images_diy ALTER position DROP DEFAULT');
        $this->addSql('ALTER TABLE content_works_images ALTER cover DROP DEFAULT');
        $this->addSql('ALTER TABLE content_works_images ALTER position DROP DEFAULT');
        $this->addSql('ALTER TABLE ecommerce_product_images ALTER cover DROP DEFAULT');
        $this->addSql('ALTER TABLE ecommerce_product_images ALTER position DROP DEFAULT');
        $this->addSql('ALTER TABLE ecommerce_product_recommended ALTER position DROP DEFAULT');
        $this->addSql('ALTER TABLE ecommerce_product ALTER info_weight DROP DEFAULT');
        $this->addSql('ALTER TABLE ecommerce_product ALTER info_weight_is_final DROP DEFAULT');
        $this->addSql('ALTER TABLE ecommerce_product ALTER info_volume DROP DEFAULT');
        $this->addSql('ALTER TABLE ecommerce_product ALTER info_volume_is_final DROP DEFAULT');
        $this->addSql('ALTER TABLE ecommerce_product ALTER price_current DROP DEFAULT');
        $this->addSql('ALTER TABLE ecommerce_product ALTER price_old DROP DEFAULT');
        $this->addSql('ALTER TABLE ecommerce_product_category ALTER main DROP DEFAULT');
        $this->addSql('ALTER TABLE ecommerce_product_composition ALTER count DROP DEFAULT');
        $this->addSql('ALTER TABLE ecommerce_product_composition ALTER position DROP DEFAULT');
        $this->addSql('ALTER TABLE sliders ALTER enable DROP DEFAULT');
        $this->addSql('ALTER TABLE sliders ALTER button_enable DROP DEFAULT');
        $this->addSql('ALTER TABLE order_product ALTER count DROP DEFAULT');
        $this->addSql('ALTER TABLE order_product ALTER price DROP DEFAULT');
        $this->addSql('ALTER TABLE order_product ALTER volume DROP DEFAULT');
        $this->addSql('ALTER TABLE order_product ALTER weight DROP DEFAULT');
        $this->addSql('ALTER TABLE order_invoice ALTER sum DROP DEFAULT');
        $this->addSql('ALTER TABLE content_gallery_images ALTER cover DROP DEFAULT');
        $this->addSql('ALTER TABLE content_gallery_images ALTER position DROP DEFAULT');
        $this->addSql('ALTER TABLE content_works_composition ALTER count DROP DEFAULT');
        $this->addSql('ALTER TABLE content_works_composition ALTER position DROP DEFAULT');
    }
}
