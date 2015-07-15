<?php
/**
 * This file is part of the ramsey/uuid library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link https://benramsey.com/projects/ramsey-uuid/ Documentation
 * @link https://packagist.org/packages/ramsey/uuid Packagist
 * @link https://github.com/ramsey/uuid GitHub
 */

namespace Ramsey\Uuid;

interface UuidFactoryInterface
{
    public function uuid1($node = null, $clockSeq = null);

    public function uuid3($ns, $name);

    public function uuid4();

    public function uuid5($ns, $name);

    public function fromBytes($bytes);

    public function fromString($name);

    public function fromInteger($integer);
}
