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

namespace Ramsey\Uuid\Generator;

use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Provider\NodeProviderInterface;
use Ramsey\Uuid\Provider\Time\SystemTimeProvider;
use Ramsey\Uuid\Provider\TimeProviderInterface;

use function extension_loaded;

/**
 * TimeGeneratorFactory retrieves a default time generator, based on the environment
 */
class TimeGeneratorFactory
{
    public function __construct(
        private NodeProviderInterface $nodeProvider,
        private TimeConverterInterface $timeConverter,
        private TimeProviderInterface $timeProvider,
    ) {
    }

    /**
     * Returns a default time generator, based on the current environment
     */
    public function getGenerator(): TimeGeneratorInterface
    {
        // ext-fast_uuid reads the system clock itself, so it cannot honor any other time provider.
        if ($this->timeProvider::class === SystemTimeProvider::class && extension_loaded('fast_uuid')) {
            return new FastUuidTimeGenerator($this->nodeProvider);
        }

        return new DefaultTimeGenerator($this->nodeProvider, $this->timeConverter, $this->timeProvider);
    }
}
