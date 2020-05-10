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
    const TABLE_NAME = 'locations';

    public function getDescription() : string
    {
        return 'Create locations table';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $table = $schema->createTable(self::TABLE_NAME);
        $table->addColumn('location_id', 'string')->setLength(36);
        $table->addColumn('coordinates', 'string')->setLength(20);
        $table->addColumn('country', 'string')->setLength(30);
        $table->addColumn('city', 'string')->setLength(30);
        $table->addColumn('created_at', 'datetime');
        $table->addColumn('updated_at', 'datetime')->setNotnull(false);

        $table->setPrimaryKey(['location_id']);
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $schema->dropTable(self::TABLE_NAME);
    }
}
