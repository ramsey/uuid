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

use DateTimeImmutable;
use Ramsey\Uuid\Provider\Node\StaticNodeProvider;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Type\Integer as IntegerIdentifier;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class UuidGenerationBench
{
    /** @var Hexadecimal */
    private $node;
    /** @var int */
    private $clockSequence;
    /** @var IntegerIdentifier */
    private $localIdentifier;
    /** @var UuidInterface */
    private $namespace;

    public function __construct()
    {
        $this->node = (new StaticNodeProvider(new Hexadecimal('121212121212')))
            ->getNode();
        $this->clockSequence = 16383;
        $this->localIdentifier = new IntegerIdentifier(5);
        $this->namespace = Uuid::fromString('c485840e-9389-4548-a276-aeecd9730e50');
    }

    public function benchUuid1GenerationWithoutParameters(): void
    {
        Uuid::uuid1();
    }

    public function benchUuid1GenerationWithNode(): void
    {
        Uuid::uuid1($this->node);
    }

    public function benchUuid1GenerationWithNodeAndClockSequence(): void
    {
        Uuid::uuid1($this->node, $this->clockSequence);
    }

    public function benchUuid2GenerationWithDomainAndLocalIdentifier(): void
    {
        Uuid::uuid2(Uuid::DCE_DOMAIN_ORG, $this->localIdentifier);
    }

    public function benchUuid2GenerationWithDomainAndLocalIdentifierAndNode(): void
    {
        Uuid::uuid2(Uuid::DCE_DOMAIN_ORG, $this->localIdentifier, $this->node);
    }

    public function benchUuid2GenerationWithDomainAndLocalIdentifierAndNodeAndClockSequence(): void
    {
        Uuid::uuid2(Uuid::DCE_DOMAIN_ORG, $this->localIdentifier, $this->node, 63);
    }

    public function benchUuid3Generation(): void
    {
        Uuid::uuid3($this->namespace, 'name');
    }

    public function benchUuid4Generation(): void
    {
        Uuid::uuid4();
    }

    public function benchUuid5Generation(): void
    {
        Uuid::uuid5($this->namespace, 'name');
    }

    public function benchUuid6GenerationWithoutParameters(): void
    {
        Uuid::uuid6();
    }

    public function benchUuid6GenerationWithNode(): void
    {
        Uuid::uuid6($this->node);
    }

    public function benchUuid6GenerationWithNodeAndClockSequence(): void
    {
        Uuid::uuid6($this->node, $this->clockSequence);
    }

    public function benchUuid7Generation(): void
    {
        Uuid::uuid7();
    }

    public function benchUuid7GenerationWithDateTime(): void
    {
        Uuid::uuid7(new DateTimeImmutable('@1663203901.667000'));
    }
}
