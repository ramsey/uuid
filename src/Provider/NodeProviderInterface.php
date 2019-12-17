<?php

/**
 * This file is part of the ramsey/uuid library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace Ramsey\Uuid\Provider;

/**
 * A node provider retrieves the system node ID
 *
 * The system node ID, or host ID, is often the same as the MAC address for a
 * network interface on the host.
 */
interface NodeProviderInterface
{
    /**
     * Returns the system node ID
     *
     * @return string|false|null System node ID as a hexadecimal string
     */
    public function getNode();
}
