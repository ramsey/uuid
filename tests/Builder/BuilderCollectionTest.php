<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Builder;

use Ramsey\Uuid\Builder\BuilderCollection;
use Ramsey\Uuid\Exception\UnsupportedOperationException;
use Ramsey\Uuid\Test\TestCase;

class BuilderCollectionTest extends TestCase
{
    /**
     * @dataProvider provideInvalidSerializedValues
     */
    public function testUnserializeThrowsExceptionForInvalidValue(string $serialized): void
    {
        $collection = new BuilderCollection();

        $this->expectException(UnsupportedOperationException::class);
        $this->expectExceptionMessage('Attempted to unserialize an invalid value');

        @$collection->unserialize($serialized);
    }

    /**
     * @return array<string, array{string}>
     */
    public function provideInvalidSerializedValues(): array
    {
        return [
            'not a serialized value' => ['garbage'],
            'a serialized string' => [serialize('foo')],
            'a serialized integer' => [serialize(42)],
        ];
    }
}
