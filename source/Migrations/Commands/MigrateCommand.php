<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
namespace Spiral\Migrations\Commands;

use Spiral\Migrations\Commands\Prototypes\AbstractCommand;
use Symfony\Component\Console\Input\InputOption;

class MigrateCommand extends AbstractCommand
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'migrate';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Perform one or all outstanding migrations';

    /**
     * {@inheritdoc}
     */
    protected $options = [
        ['one', 'o', InputOption::VALUE_NONE, 'Execute only one (first) migration']
    ];

    /**
     * Execute one or multiple migrations.
     */
    public function perform()
    {
        if (!$this->verifyConfigured() || !$this->verifyEnvironment()) {
            return;
        }

        $found = false;
        $count = $this->option('one') ? 1 : PHP_INT_MAX;

        while ($count > 0 && ($migration = $this->migrator->run())) {
            $found = true;
            $count--;

            $this->writeln(interpolate(
                "<info>Migration <comment>{name}</comment> was successfully executed.</info>",
                ['name' => $migration->getMeta()->getName()]
            ));
        }

        if (!$found) {
            $this->writeln("<fg=red>No outstanding migrations were found.</fg=red>");
        }
    }
}