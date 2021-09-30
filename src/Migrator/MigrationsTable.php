<?php

/**
 * This file is part of Cycle ORM package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Cycle\Migrations\Migrator;

use Cycle\Database\Database;
use Cycle\Database\Schema\AbstractTable;

/**
 * This class is responsible for managing the migration table within a
 * specific database.
 *
 * @internal MigrationsTable is an internal library class, please do not use it in your code.
 * @psalm-internal Cycle\Migrations
 */
class MigrationsTable
{
    /**
     * List of fields in the migration table. An implementation is specified in
     * the {@see MigrationsTable::actualize()} method.
     *
     * @var array<non-empty-string>
     */
    private const MIGRATION_TABLE_FIELDS = [
        'id',
        'migration',
        'time_executed',
        'created_at',
    ];

    /**
     * List of indices in the migration table. An implementation is specified in
     * the {@see MigrationsTable::actualize()} method.
     *
     * @var array<non-empty-string>
     */
    private const MIGRATION_TABLE_INDICES = [
        'migration',
        'created_at',
    ];

    /**
     * @var Database
     */
    private $db;

    /**
     * @var string
     */
    private $name;

    /**
     * @var AbstractTable
     */
    private $schema;

    /**
     * @param Database $db
     * @param string $name
     */
    public function __construct(Database $db, string $name)
    {
        $this->db = $db;
        $this->name = $name;

        $table = $db->table($name);
        $this->schema = $table->getSchema();
    }

    /**
     * Schema update will automatically sync all needed data.
     *
     * Please note that if you change this migration, you will also need to
     * change the list of fields in this migration specified in
     * the {@see MigrationsTable::MIGRATION_TABLE_FIELDS}
     * and {@see MigrationsTable::MIGRATION_TABLE_INDICES} constants.
     */
    public function actualize(): void
    {
        $this->schema->primary('id');
        $this->schema->string('migration', 191)->nullable(false);
        $this->schema->datetime('time_executed')->datetime();
        $this->schema->datetime('created_at')->datetime();

        $this->schema->index(['migration', 'created_at'])
            ->unique(true);

        if ($this->schema->hasIndex(['migration'])) {
            $this->schema->dropIndex(['migration']);
        }

        $this->schema->save();
    }

    /**
     * Returns {@see true} if the migration table in the database is up to date
     * or {@see false} instead.
     *
     * @return bool
     */
    public function isPresent(): bool
    {
        if (!$this->isTableExists()) {
            return false;
        }

        if (!$this->isNecessaryColumnsExists()) {
            return false;
        }

        return ! (!$this->isNecessaryIndicesExists());
    }

    /**
     * Returns {@see true} if the migration's table exists or {@see false}
     * instead.
     *
     * @return bool
     */
    private function isTableExists(): bool
    {
        return $this->db->hasTable($this->name);
    }

    /**
     * Returns {@see true} if all migration's fields is present or {@see false}
     * otherwise.
     *
     * @return bool
     */
    private function isNecessaryColumnsExists(): bool
    {
        foreach (self::MIGRATION_TABLE_FIELDS as $field) {
            if (!$this->schema->hasColumn($field)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns {@see true} if all migration's indices is present or {@see false}
     * otherwise.
     *
     * @return bool
     */
    private function isNecessaryIndicesExists(): bool
    {
        return $this->schema->hasIndex(self::MIGRATION_TABLE_INDICES);
    }
}
