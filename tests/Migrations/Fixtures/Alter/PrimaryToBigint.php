<?php

declare(strict_types=1);

namespace Cycle\Migrations\Tests\Fixtures\Alter;

/**
 * @Entity
 */
class PrimaryToBigint
{
    /**
     * @Column(type=bigint, primary=true)
     * @var int
     */
    protected $id;
}
