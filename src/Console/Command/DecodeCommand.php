<?php
/**
 * This file is part of the Rhumsaa\Uuid library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) 2013-2014 Ben Ramsey <http://benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace Rhumsaa\Uuid\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\TableHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Rhumsaa\Uuid\Console\Exception;
use Rhumsaa\Uuid\Uuid;

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

        $table->addRows(array(
            array('encode:', 'STR:', (string) $uuid),
            array('',        'INT:', (string) $uuid->getInteger()),
        ));

        if ($uuid->getVariant() != Uuid::RFC_4122) {

            $table->addRows(array(
                array('decode:', 'variant:', 'Not an RFC 4122 UUID'),
            ));

            $table->render($output);

            return;
        }

        switch ($uuid->getVersion()) {
            case 1:
                $version = '1 (time and node based)';
                break;
            case 2:
                $version = '2 (DCE security based)';
                break;
            case 3:
                $version = '3 (name based, MD5)';
                break;
            case 4:
                $version = '4 (random data based)';
                break;
            case 5:
                $version = '5 (name based, SHA-1)';
                break;
        }

        $table->addRows(array(
            array('decode:', 'variant:', 'RFC 4122'),
            array('',        'version:', $version),
        ));

        if ($uuid->getVersion() == 1) {
            $table->addRows(array(
                array('', 'content:', 'time:  ' . $uuid->getDateTime()->format('c')),
                array('', '', 'clock: ' . $uuid->getClockSequence() . ' (usually random)'),
                array('', '', 'node:  ' . substr(chunk_split($uuid->getNodeHex(), 2, ':'), 0, -1)),
            ));
        }

        if ($uuid->getVersion() == 4) {
            $table->addRows(array(
                array('', 'content:', substr(chunk_split($uuid->getHex(), 2, ':'), 0, -1)),
                array('', '', '(no semantics: random data only)'),
            ));
        }

        if ($uuid->getVersion() == 3 || $uuid->getVersion() == 5) {
            $table->addRows(array(
                array('', 'content:', substr(chunk_split($uuid->getHex(), 2, ':'), 0, -1)),
                array('', '', '(not decipherable: MD5 message digest only)'),
            ));
        }

        $table->render($output);
    }
}
