<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test;

use DateTimeImmutable;
use Ramsey\Uuid\FeatureSet;
use Ramsey\Uuid\Generator\FastUuidRandomGenerator;
use Ramsey\Uuid\Generator\RandomGeneratorInterface;
use Ramsey\Uuid\Generator\UnixTimeGenerator;
use Ramsey\Uuid\Guid\Guid;
use Ramsey\Uuid\Lazy\LazyUuidFromString;
use Ramsey\Uuid\Rfc4122\FieldsInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactory;

use function microtime;
use function ord;
use function str_repeat;
use function strcmp;
use function usleep;

/**
 * @requires extension fast_uuid
 */
class FastUuidUuid7Test extends TestCase
{
    public function testDefaultFactoryReturnsLazyVersion7UuidsForTheCurrentTime(): void
    {
        $before = (int) (microtime(true) * 1000);
        $uuid = Uuid::uuid7();
        $after = (int) (microtime(true) * 1000);

        $this->assertInstanceOf(LazyUuidFromString::class, $uuid);

        $fields = $uuid->getFields();
        $this->assertInstanceOf(FieldsInterface::class, $fields);
        $this->assertSame(7, $fields->getVersion());
        $this->assertSame(Uuid::RFC_4122, $fields->getVariant());

        $milliseconds = (int) $uuid->getDateTime()->format('Uv');
        $this->assertGreaterThanOrEqual($before, $milliseconds);
        $this->assertLessThanOrEqual($after, $milliseconds);
    }

    public function testDefaultAndCustomFactoriesShareOneIncreasingSequence(): void
    {
        $custom = new UuidFactory(new FeatureSet());
        $previous = Uuid::uuid7()->toString();

        for ($i = 0; $i < 20000; $i++) {
            $uuid = ($i % 2 === 0 ? $custom->uuid7() : Uuid::uuid7())->toString();
            $this->assertGreaterThan(0, strcmp($uuid, $previous), "$uuid is not after $previous");
            $previous = $uuid;
        }
    }

    public function testFactoryWithGuidsKeepsItsCodec(): void
    {
        $uuid = (new UuidFactory(new FeatureSet(useGuids: true)))->uuid7();

        $this->assertInstanceOf(Guid::class, $uuid);

        $fields = $uuid->getFields();
        $this->assertInstanceOf(FieldsInterface::class, $fields);
        $this->assertSame(7, $fields->getVersion());
    }

    public function testUnixTimeGeneratorLeavesVersionAndVariantBitsZero(): void
    {
        $bytes = (new UnixTimeGenerator(new FastUuidRandomGenerator()))->generate();

        $this->assertSame(0, ord($bytes[6]) & 0xf0);
        $this->assertSame(0, ord($bytes[8]) & 0xc0);
    }

    public function testUnixTimeGeneratorUsesAnotherRandomGenerator(): void
    {
        $randomGenerator = new class implements RandomGeneratorInterface {
            public int $calls = 0;

            public function generate(int $length): string
            {
                $this->calls++;

                return str_repeat("\x00", $length);
            }
        };

        // A new millisecond starts a new counter, which draws random bytes.
        usleep(2000);
        (new UnixTimeGenerator($randomGenerator))->generate();

        $this->assertGreaterThan(0, $randomGenerator->calls);
    }

    public function testUuidsForOneDateTimeStillIncrease(): void
    {
        $dateTime = new DateTimeImmutable('@1700000000.123456');
        $previous = Uuid::uuid7($dateTime)->toString();

        for ($i = 0; $i < 1000; $i++) {
            $uuid = Uuid::uuid7($dateTime)->toString();
            $this->assertGreaterThan(0, strcmp($uuid, $previous), "$uuid is not after $previous");
            $this->assertSame('1700000000123', Uuid::fromString($uuid)->getDateTime()->format('Uv'));
            $previous = $uuid;
        }
    }
}
