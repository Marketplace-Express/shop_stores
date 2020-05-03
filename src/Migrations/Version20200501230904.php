<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200501230904 extends AbstractMigration
{
    const TABLE_NAME = 'followers';
    const REFERENCE_TABLE_STORES = 'stores';

    public function getDescription() : string
    {
        return 'Create followers table';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $table = $schema->createTable(self::TABLE_NAME);
        $table->addColumn('id', 'bigint')->setAutoincrement(true);
        $table->addColumn('store_id', 'string')->setNotnull(true)->setLength(36);
        $table->addColumn('follower_id', 'string')->setNotnull(true)->setLength(36);
        $table->addColumn('followed_at', 'datetime')->setNotnull(true);

        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['store_id', 'follower_id'], 'unique_follower_index');
        $table->addForeignKeyConstraint(self::REFERENCE_TABLE_STORES, ['store_id'], ['store_id']);
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $schema->dropTable(self::TABLE_NAME);
    }
}
