<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200416122659 extends AbstractMigration
{
    const TABLE_NAME = 'vendors';

    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $table = $schema->createTable(self::TABLE_NAME);
        $table->addColumn('vendor_id', 'string', ['length' => 36]);
        $table->addColumn('owner_id', 'string', ['length' => 36, 'notNull' => false]);

        $table->setPrimaryKey(['vendor_id']);
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $schema->dropTable(self::TABLE_NAME);
    }
}
