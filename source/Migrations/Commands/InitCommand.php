<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
namespace Spiral\Migrations\Commands;

use Spiral\Migrations\Commands\Prototypes\AbstractCommand;

class InitCommand extends AbstractCommand
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'migrate:init';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Init migrations component (create migrations table)';

    /**
     * Perform command.
     */
    public function perform()
    {
        $this->migrator->configure();
        $this->writeln("<info>Migrations table were successfully created</info>");
    }
}