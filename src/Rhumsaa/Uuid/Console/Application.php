<?php
/**
 * This file is part of the Rhumsaa\Uuid library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) 2013 Ben Ramsey <http://benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace Rhumsaa\Uuid\Console;

use Rhumsaa\Uuid\Console\Command;
use Rhumsaa\Uuid\Console\Util;
use Rhumsaa\Uuid\Uuid;
use Symfony\Component\Console\Application as BaseApplication;

/**
 * The console application that handles CLI commands
 */
class Application extends BaseApplication
{
    /**
     * Constructor
     */
    public function __construct()
    {
        if (function_exists('date_default_timezone_set') && function_exists('date_default_timezone_get')) {
            date_default_timezone_set(@date_default_timezone_get());
        }

        Util\ErrorHandler::register();
        parent::__construct('uuid', Uuid::VERSION);
    }

    /**
     * Initializes all the commands we have
     *
     * @return array The array of Command classes
     */
    protected function getDefaultCommands()
    {
        $commands   = parent::getDefaultCommands();
        $commands[] = new Command\GenerateCommand();

        return $commands;
    }
}
