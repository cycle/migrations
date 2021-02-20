<?php

declare(strict_types=1);

namespace Cycle\Migrations\Tests\Fixtures\Init;

/**
 * @Entity
 */
class PrimaryToBigint
{
    /**
     * @Column(type=primary)
     * @var int
     */
    protected $id;
}
