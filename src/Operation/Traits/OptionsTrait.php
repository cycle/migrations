<?php

declare(strict_types=1);

namespace Cycle\Migrations\Operation\Traits;

/**
 * Consumer must define property "aliases".
 */
trait OptionsTrait
{
    protected array $options = [];

    protected function hasOption(string $name): bool
    {
        if (\array_key_exists($name, $this->options)) {
            return true;
        }

        if (!isset($this->aliases[$name])) {
            return false;
        }

        foreach ($this->aliases as $source => $aliases) {
            if (\in_array($name, $aliases, true)) {
                return true;
            }
        }

        return false;
    }

    protected function getOption(string $name, mixed $default = null): mixed
    {
        if (!$this->hasOption($name)) {
            return $default;
        }

        if (\array_key_exists($name, $this->options)) {
            return $this->options[$name];
        }

        if (!isset($this->aliases[$name])) {
            return $default;
        }

        foreach ($this->aliases as $source => $aliases) {
            if (\in_array($name, $aliases, true)) {
                return $this->getOption($source);
            }
        }

        return false;
    }
}
