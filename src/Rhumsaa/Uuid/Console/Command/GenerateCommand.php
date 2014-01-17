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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Rhumsaa\Uuid\Console\Exception;
use Rhumsaa\Uuid\Uuid;

/**
 * Provides the console command to generate UUIDs
 */
class GenerateCommand extends Command
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('generate')
            ->setDescription('Generate a UUID')
            ->addArgument(
                'version',
                InputArgument::OPTIONAL,
                'The UUID version to generate. Supported are version "1", "3", '
                . '"4" and "5".',
                1
            )
            ->addArgument(
                'namespace',
                InputArgument::OPTIONAL,
                'For version 3 or 5 UUIDs, the namespace to create a UUID for. '
                . 'May be either a UUID in string representation or an identifier '
                . 'for internally pre-defined namespace UUIDs (currently known '
                . 'are "ns:DNS", "ns:URL", "ns:OID", and "ns:X500").'
            )
            ->addArgument(
                'name',
                InputArgument::OPTIONAL,
                'For version 3 or 5 UUIDs, the name to create a UUID for. '
                . 'The name is a string of arbitrary length.'
            )
            ->addOption(
                'count',
                'c',
                InputOption::VALUE_REQUIRED,
                'Generate count UUIDs instead of just a single one.',
                1
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
        $uuids = array();

        $count = filter_var(
            $input->getOption('count'),
            FILTER_VALIDATE_INT,
            array(
                'default' => 1,
                'min_range' => 1,
            )
        );

        for ($i = 0; $i < $count; $i++) {
            $uuids[] = $this->createUuid(
                $input->getArgument('version'),
                $input->getArgument('namespace'),
                $input->getArgument('name')
            );
        }

        foreach ($uuids as $uuid) {
            $output->writeln((string) $uuid);
        }
    }

    /**
     * Creates the requested UUID
     *
     * @param int $version
     * @param string $namespace
     * @param string $name
     * @return Uuid
     */
    protected function createUuid($version, $namespace = null, $name = null)
    {
        switch ((int) $version) {
            case 1:
                $uuid = Uuid::uuid1();
                break;
            case 4:
                $uuid = Uuid::uuid4();
                break;
            case 3:
            case 5:
                $ns = $this->validateNamespace($namespace);
                if (empty($name)) {
                    throw new Exception('The name argument is required for version 3 or 5 UUIDs');
                }
                if ($version == 3) {
                    $uuid = Uuid::uuid3($ns, $name);
                } else {
                    $uuid = Uuid::uuid5($ns, $name);
                }
                break;
            default:
                throw new Exception('Invalid UUID version. Supported are version "1", "3", "4", and "5".');
        }

        return $uuid;
    }

    /**
     * Validates the namespace argument
     *
     * @param string $namespace
     * @return string The namespace, if valid
     * @throws Exception
     */
    protected function validateNamespace($namespace)
    {
        switch ($namespace) {
            case 'ns:DNS':
                return Uuid::NAMESPACE_DNS;
                break;
            case 'ns:URL':
                return Uuid::NAMESPACE_URL;
                break;
            case 'ns:OID':
                return Uuid::NAMESPACE_OID;
                break;
            case 'ns:X500':
                return Uuid::NAMESPACE_X500;
                break;
        }

        if (Uuid::isValid($namespace)) {
            return $namespace;
        }

        throw new Exception('Invalid namespace. '
            . 'May be either a UUID in string representation or an identifier '
            . 'for internally pre-defined namespace UUIDs (currently known '
            . 'are "ns:DNS", "ns:URL", "ns:OID", and "ns:X500").');
    }
}
