<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
namespace Spiral\Migrations\Commands;

use Spiral\Migrations\Commands\Prototypes\AbstractCommand;
use Spiral\Migrations\Migration\Meta;

/**
 * Show all available migrations and their statuses
 */
class StatusCommand extends AbstractCommand
{
    /**
     * Text to show if migration is not performed.
     */
    const PENDING = '<fg=red>not executed yet</fg=red>';

    /**
     * {@inheritdoc}
     */
    protected $name = 'migrate:status';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Get list of all available migrations and their statuses';

    /**
     * Perform command.
     */
    public function perform()
    {
        if (!$this->verifyConfigured()) {
            return;
        }

        if (empty($this->migrator->getMigrations())) {
            $this->writeln("No migrations were found.");

            return;
        }

        $table = $this->tableHelper(['Migration', 'Filename', 'Created at', 'Executed at']);
        foreach ($this->migrator->getMigrations() as $migration) {
            $filename = (new \ReflectionClass($migration))->getFileName();

            $meta = $migration->getMeta();

            $table->addRow([
                $meta->getName(),
                '<comment>'
                . $this->files->relativePath($filename, $this->config->getDirectory())
                . '</comment>',
                $meta->getTimeCreated()->format('Y-m-d H:i:s'),
                $meta->getStatus() == Meta::STATUS_PENDING
                    ? self::PENDING
                    : '<info>' . $meta->getTimeExecuted()->format('Y-m-d H:i:s') . '</info>'
            ]);
        }

        $table->render();
    }
}