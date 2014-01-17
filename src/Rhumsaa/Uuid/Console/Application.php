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

namespace Rhumsaa\Uuid\Console;

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
        Util\ErrorHandler::register();
        parent::__construct('uuid', Uuid::VERSION);
    }
}
