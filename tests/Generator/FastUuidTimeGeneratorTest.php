<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Generator;

use DateTimeImmutable;
use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Generator\FastUuidTimeGenerator;
use Ramsey\Uuid\Provider\Node\StaticNodeProvider;
use Ramsey\Uuid\Rfc4122\FieldsInterface;
use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Uuid;

use function abs;
use function bin2hex;
use function substr;

/**
 * @requires extension fast_uuid
 */
class FastUuidTimeGeneratorTest extends TestCase
{
    private function generator(): FastUuidTimeGenerator
    {
        return new FastUuidTimeGenerator(new StaticNodeProvider(new Hexadecimal('0123456789ab')));
    }

    public function testGenerateUsesNodeProviderAndCurrentTime(): void
    {
        $bytes = $this->generator()->generate();
        $uuid = Uuid::fromBytes($bytes);

        /** @var FieldsInterface $fields */
        $fields = $uuid->getFields();

        $this->assertSame(1, $fields->getVersion());
        $this->assertSame(Uuid::RFC_4122, $fields->getVariant());
        $this->assertSame('0123456789ab', bin2hex(substr($bytes, 10)));

        $delta = $uuid->getDateTime()->getTimestamp() - (new DateTimeImmutable())->getTimestamp();
        $this->assertLessThan(5, abs($delta));
    }

    /**
     * @param Hexadecimal | int | string $node
     *
     * @dataProvider provideNodes
     */
    public function testGenerateUsesGivenNode($node, string $expected): void
    {
        $bytes = $this->generator()->generate($node, 0x1234);

        $this->assertSame($expected, bin2hex(substr($bytes, 10)));
        $this->assertSame('9234', bin2hex(substr($bytes, 8, 2)));
    }

    /**
     * @return array<array{Hexadecimal | int | string, string}>
     */
    public function provideNodes(): array
    {
        return [
            ['aabbccddeeff', 'aabbccddeeff'],
            ['AABBCCDDEEFF', 'aabbccddeeff'],
            ['abc', '000000000abc'],
            [new Hexadecimal('010203040506'), '010203040506'],
            [1, '000000000001'],
        ];
    }

    public function testGenerateKeepsLow14BitsOfClockSequence(): void
    {
        $bytes = $this->generator()->generate(null, 0xffff);

        $this->assertSame('bfff', bin2hex(substr($bytes, 8, 2)));
    }

    public function testGenerateReturnsDifferentBytesForFixedNodeAndClockSequence(): void
    {
        $generator = $this->generator();

        $this->assertNotSame($generator->generate(null, 1), $generator->generate(null, 1));
    }

    /**
     * @dataProvider provideInvalidNodes
     */
    public function testGenerateThrowsForInvalidNode(string $node): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid node value');

        $this->generator()->generate($node);
    }

    /**
     * @return array<array{string}>
     */
    public function provideInvalidNodes(): array
    {
        return [['xyz'], ['0123456789abc'], ["0123456789ab\n"], ['']];
    }
}
