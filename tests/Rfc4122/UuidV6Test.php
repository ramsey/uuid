<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Rfc4122;

use DateTimeImmutable;
use Mockery;
use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Exception\DateTimeException;
use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Lazy\LazyUuidFromString;
use Ramsey\Uuid\Rfc4122\FieldsInterface;
use Ramsey\Uuid\Rfc4122\UuidV6;
use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Type\Time;
use Ramsey\Uuid\Uuid;

class UuidV6Test extends TestCase
{
    /**
     * @dataProvider provideTestVersions
     */
    public function testConstructorThrowsExceptionWhenFieldsAreNotValidForType(int $version): void
    {
        $fields = Mockery::mock(FieldsInterface::class, [
            'getVersion' => $version,
        ]);

        $numberConverter = Mockery::mock(NumberConverterInterface::class);
        $codec = Mockery::mock(CodecInterface::class);
        $timeConverter = Mockery::mock(TimeConverterInterface::class);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Fields used to create a UuidV6 must represent a '
            . 'version 6 (reordered time) UUID'
        );

        new UuidV6($fields, $numberConverter, $codec, $timeConverter);
    }

    /**
     * @return array<array{version: int}>
     */
    public function provideTestVersions(): array
    {
        return [
            ['version' => 0],
            ['version' => 1],
            ['version' => 2],
            ['version' => 3],
            ['version' => 4],
            ['version' => 5],
            ['version' => 7],
            ['version' => 8],
            ['version' => 9],
        ];
    }

    /**
     * @param non-empty-string $uuid
     * @param non-empty-string $expected
     *
     * @dataProvider provideUuidV6WithOddMicroseconds
     */
    public function testGetDateTimeProperlyHandlesLongMicroseconds(string $uuid, string $expected): void
    {
        /** @var UuidV6 $object */
        $object = Uuid::fromString($uuid);

        $date = $object->getDateTime();

        $this->assertInstanceOf(DateTimeImmutable::class, $date);
        $this->assertSame($expected, $date->format('U.u'));
    }

    /**
     * @return array<array{uuid: non-empty-string, expected: non-empty-string}>
     */
    public function provideUuidV6WithOddMicroseconds(): array
    {
        return [
            [
                'uuid' => '1b21dd21-4814-6000-9669-00007ffffffe',
                'expected' => '1.677722',
            ],
            [
                'uuid' => '1b21dd21-3714-6000-9669-00007ffffffe',
                'expected' => '0.104858',
            ],
            [
                'uuid' => '1b21dd21-3713-6000-9669-00007ffffffe',
                'expected' => '0.105267',
            ],
            [
                'uuid' => '1b21dd21-2e8a-6980-8d4f-acde48001122',
                'expected' => '-1.000000',
            ],
        ];
    }

    /**
     * @param non-empty-string $uuidv6
     * @param non-empty-string $uuidv1
     *
     * @dataProvider provideUuidV1UuidV6Equivalents
     */
    public function testToUuidV1(string $uuidv6, string $uuidv1): void
    {
        /** @var UuidV6 $uuid6 */
        $uuid6 = Uuid::fromString($uuidv6);
        $uuid1 = $uuid6->toUuidV1();

        $this->assertSame($uuidv6, $uuid6->toString());
        $this->assertSame($uuidv1, $uuid1->toString());

        $this->assertSame(
            $uuid6->getDateTime()->format('U.u'),
            $uuid1->getDateTime()->format('U.u')
        );
    }

    /**
     * @param non-empty-string $uuidv6
     * @param non-empty-string $uuidv1
     *
     * @dataProvider provideUuidV1UuidV6Equivalents
     */
    public function testFromUuidV1(string $uuidv6, string $uuidv1): void
    {
        /** @var LazyUuidFromString $uuid */
        $uuid = Uuid::fromString($uuidv1);
        $uuid1 = $uuid->toUuidV1();
        $uuid6 = UuidV6::fromUuidV1($uuid1);

        $this->assertSame($uuidv1, $uuid1->toString());
        $this->assertSame($uuidv6, $uuid6->toString());

        $this->assertSame(
            $uuid1->getDateTime()->format('U.u'),
            $uuid6->getDateTime()->format('U.u')
        );
    }

    /**
     * @return array<array{uuidv6: non-empty-string, uuidv1: non-empty-string}>
     */
    public function provideUuidV1UuidV6Equivalents(): array
    {
        return [
            [
                'uuidv6' => '1b21dd21-4814-6000-9669-00007ffffffe',
                'uuidv1' => '14814000-1dd2-11b2-9669-00007ffffffe',
            ],
            [
                'uuidv6' => '1b21dd21-3714-6000-9669-00007ffffffe',
                'uuidv1' => '13714000-1dd2-11b2-9669-00007ffffffe',
            ],
            [
                'uuidv6' => '1b21dd21-3713-6000-9669-00007ffffffe',
                'uuidv1' => '13713000-1dd2-11b2-9669-00007ffffffe',
            ],
            [
                'uuidv6' => '1b21dd21-2e8a-6980-8d4f-acde48001122',
                'uuidv1' => '12e8a980-1dd2-11b2-8d4f-acde48001122',
            ],
        ];
    }

    public function testGetDateTimeThrowsException(): void
    {
        $fields = Mockery::mock(FieldsInterface::class, [
            'getVersion' => 6,
            'getTimestamp' => new Hexadecimal('0'),
        ]);

        $numberConverter = Mockery::mock(NumberConverterInterface::class);
        $codec = Mockery::mock(CodecInterface::class);

        $timeConverter = Mockery::mock(TimeConverterInterface::class, [
            'convertTime' => new Time('0', '1234567'),
        ]);

        $uuid = new UuidV6($fields, $numberConverter, $codec, $timeConverter);

        $this->expectException(DateTimeException::class);

        $uuid->getDateTime();
    }

    /**
     * @link https://datatracker.ietf.org/doc/html/draft-peabody-dispatch-new-uuid-format-04#appendix-B.1
     */
    public function testUsingDraftPeabodyUuidV6TestVector(): void
    {
        $testVector = '1EC9414C-232A-6B00-B3C8-9E6BDECED846';

        /** @var UuidV6 $uuidv6 */
        $uuidv6 = Uuid::fromString($testVector);

        $uuidv1 = $uuidv6->toUuidV1();

        /** @var FieldsInterface $fields */
        $fields = $uuidv6->getFields();

        $this->assertSame('1ec9414c', $fields->getTimeLow()->toString());
        $this->assertSame('232a', $fields->getTimeMid()->toString());
        $this->assertSame('6b00', $fields->getTimeHiAndVersion()->toString());
        $this->assertSame('b3', $fields->getClockSeqHiAndReserved()->toString());
        $this->assertSame('c8', $fields->getClockSeqLow()->toString());
        $this->assertSame('9e6bdeced846', $fields->getNode()->toString());
        $this->assertSame(1645557742, $uuidv6->getDateTime()->getTimestamp());

        $this->assertSame(
            'c232ab00-9414-11ec-b3c8-9e6bdeced846',
            $uuidv1->toString(),
        );
    }
}
