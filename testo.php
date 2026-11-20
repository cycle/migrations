<?php

declare(strict_types=1);

use Cycle\Testing\Config;
use Cycle\Testing\Database\Layout;

return Config::forPackage('cycle/migrations', root: __DIR__)
    ->unit('tests/Unit')
    ->feature('tests/Feature', layout: Layout::prefixed(['default' => 'tests_', 'slave' => 'slave_']))
    ->build();
