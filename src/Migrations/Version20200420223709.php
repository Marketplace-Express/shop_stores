<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200420223709 extends AbstractMigration
{
    const TABLE_NAME = 'stores';

    public function getDescription() : string
    {
        return 'Create stores table';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $table = $schema->createTable(self::TABLE_NAME);
        $table->addColumn('store_id', 'string')->setLength(36);
        $table->addColumn('owner_id', 'string')->setLength(36);
        $table->addColumn('name', 'string')->setLength(100);
        $table->addColumn('description', 'text')->setNotnull(false);
        $table->addColumn('type', 'integer')->setLength(1);
        $table->addColumn('location_id', 'string')->setLength(36);
        $table->addColumn('products_count', 'integer');
        $table->addColumn('followers_count', 'integer');
        $table->addColumn('orders_count', 'integer');
        $table->addColumn('photo', 'string');
        $table->addColumn('cover_photo', 'string');
        $table->addColumn('disable_reason', 'integer')->setLength(1)->setNotnull(false);
        $table->addColumn('disable_comment', 'string')->setNotnull(false);
        $table->addColumn('created_at', 'datetime');
        $table->addColumn('updated_at', 'datetime')->setNotnull(false);
        $table->addColumn('disabled_at', 'datetime')->setNotnull(false);
        $table->addColumn('deleted_at', 'datetime')->setNotnull(false);

        // Create unique index
        $table->addUniqueIndex(['store_id', 'owner_id'], 'unique_store_index');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $schema->dropTable(self::TABLE_NAME);
    }

}
