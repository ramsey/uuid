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

namespace Ramsey\Uuid\Benchmark;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class UuidFieldExtractionBench
{
    /** @var UuidInterface */
    private $uuid;

    public function __construct()
    {
        $this->uuid = Uuid::fromString('0ae0cac5-2a40-465c-99ed-3d331b7cf72a');
    }

    public function benchGetFields(): void
    {
        $this->uuid->getFields();
    }

    public function benchGetFields10Times(): void
    {
        $this->uuid->getFields();
        $this->uuid->getFields();
        $this->uuid->getFields();
        $this->uuid->getFields();
        $this->uuid->getFields();
        $this->uuid->getFields();
        $this->uuid->getFields();
        $this->uuid->getFields();
        $this->uuid->getFields();
        $this->uuid->getFields();
    }

    public function benchGetHex(): void
    {
        $this->uuid->getHex();
    }

    public function benchGetHex10Times(): void
    {
        $this->uuid->getHex();
        $this->uuid->getHex();
        $this->uuid->getHex();
        $this->uuid->getHex();
        $this->uuid->getHex();
        $this->uuid->getHex();
        $this->uuid->getHex();
        $this->uuid->getHex();
        $this->uuid->getHex();
        $this->uuid->getHex();
    }

    public function benchGetInteger(): void
    {
        $this->uuid->getInteger();
    }

    public function benchGetInteger10Times(): void
    {
        $this->uuid->getInteger();
        $this->uuid->getInteger();
        $this->uuid->getInteger();
        $this->uuid->getInteger();
        $this->uuid->getInteger();
        $this->uuid->getInteger();
        $this->uuid->getInteger();
        $this->uuid->getInteger();
        $this->uuid->getInteger();
        $this->uuid->getInteger();
    }
}
