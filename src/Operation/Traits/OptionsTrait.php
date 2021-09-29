<?php

/**
 * This file is part of Cycle ORM package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Cycle\Migrations\Operation\Traits;

/**
 * Consumer must define property "aliases".
 */
trait OptionsTrait
{
    /** @var array */
    protected $options = [];

    /**
     * @param string $name
     *
     * @return bool
     */
    protected function hasOption(string $name): bool
    {
        if (array_key_exists($name, $this->options)) {
            return true;
        }

        if (!isset($this->aliases[$name])) {
            return false;
        }

        foreach ($this->aliases as $source => $aliases) {
            if (in_array($name, $aliases, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    protected function getOption(string $name, $default = null)
    {
        if (!$this->hasOption($name)) {
            return $default;
        }

        if (array_key_exists($name, $this->options)) {
            return $this->options[$name];
        }

        if (!isset($this->aliases[$name])) {
            return $default;
        }

        foreach ($this->aliases as $source => $aliases) {
            if (in_array($name, $aliases, true)) {
                return $this->getOption($source);
            }
        }

        return false;
    }
}
