<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Migrations\Tests;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Psr\Log\LogLevel;

class TestLogger implements LoggerInterface
{
    use LoggerTrait;

    public function log($level, $message, array $context = []): void
    {
        if ($level == LogLevel::ERROR) {
            echo " \n! \033[31m" . $message . "\033[0m";
        } elseif ($level == LogLevel::ALERT) {
            echo " \n! \033[35m" . $message . "\033[0m";
        } elseif (str_starts_with($message, 'SHOW')) {
            echo " \n> \033[34m" . $message . "\033[0m";
        } else {
            if (str_starts_with($message, 'SELECT')) {
                echo " \n> \033[32m" . $message . "\033[0m";
            } else {
                echo " \n> \033[33m" . $message . "\033[0m";
            }
        }
    }
}
