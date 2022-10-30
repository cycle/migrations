<?php

declare(strict_types=1);

namespace Cycle\Migrations\V2;

use Cycle\Database\DatabaseInterface;

class TypeBuilder
{
    public const ENUM = 'ENUM';
    public const RANGE = 'RANGE';

    private DatabaseInterface $db;
    private string $name;
    private array $values;
    private string $type;

    public function __construct(
        string $name,
        array $values,
        string $type,
        DatabaseInterface $db
    ) {
        $this->db = $db;
        $this->name = $name;
        $this->values = $values;
        $this->type = $type;
    }

    public function create(): void
    {
        $values = implode(',', array_map(static fn($v) => "'{$v}'", $this->values));

        $query = sprintf(
            'CREATE TYPE %s AS %s (%s);',
            $this->name,
            $this->type,
            $values
        );

        $this->db->execute($query);
        $this->db->commit();
    }

    public function drop(): void
    {
        $query = sprintf('DROP TYPE %s;', $this->name);
        $this->db->execute($query);
        $this->db->commit();

    }
}
