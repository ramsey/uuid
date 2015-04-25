<?php
/**
 * This file is part of the Ramsey\Uuid library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) 2012-2014 Ben Ramsey <http://benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace Ramsey\Uuid\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\TableHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Ramsey\Uuid\Console\Exception;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Console\Util\UuidFormatter;
use Symfony\Component\Console\Helper\Table;

/**
 * Provides the console command to decode UUIDs and dump information about them
 */
class DecodeCommand extends Command
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('decode')
            ->setDescription('Decode a UUID and dump information about it')
            ->addArgument(
                'uuid',
                InputArgument::REQUIRED,
                'The UUID to decode.'
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!Uuid::isValid($input->getArgument('uuid'))) {
            throw new Exception('Invalid UUID (' . $input->getArgument('uuid') . ')');
        }

        $uuid = Uuid::fromString($input->getArgument('uuid'));

        $table = $this->getHelperSet()->get('table');
        $table->setLayout(TableHelper::LAYOUT_BORDERLESS);

        (new UuidFormatter())->write($table, $uuid);

        $table->render($output);
    }
}
