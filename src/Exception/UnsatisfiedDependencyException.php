<?php
/**
 * This file is part of the Rhumsaa\Uuid library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) 2012-2014 Ben Ramsey <http://benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace Rhumsaa\Uuid\Exception;

/**
 * Thrown to indicate that the requested operation has dependencies that have not
 * been satisfied.
 */
class UnsatisfiedDependencyException extends \RuntimeException
{
}
