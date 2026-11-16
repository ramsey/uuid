<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Generator;

use Closure;
use DateTimeImmutable;
use JMac\Testing\Double;
use JMac\Testing\DoubleInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Exception\RandomSourceException;
use Ramsey\Uuid\Exception\TimeSourceException;
use Ramsey\Uuid\Generator\RandomBytesGenerator;
use Ramsey\Uuid\Generator\RandomGeneratorInterface;
use Ramsey\Uuid\Generator\UnixTimeGenerator;
use Ramsey\Uuid\Test\MocksFunctions;
use Ramsey\Uuid\Test\TestCase;

use function array_shift;
use function bin2hex;
use function count;
use function hex2bin;
use function str_repeat;
use function strlen;
use function substr;

/**
 * Every test that generates bytes runs in a separate process, since the generator stores its values statically and
 * since a function can be replaced only before the generator first calls it.
 */
class UnixTimeGeneratorTest extends TestCase
{
    use MocksFunctions;

    private const ITERATIONS = 2000;

    /**
     * The clock reading for 2020-01-09 23:25:59.521023 UTC: 0x016f8ca10161 milliseconds, with a fraction of 0x05e.
     */
    private const CLOCK = '0.52102300 1578612359';

    private const ALL_BITS_SET = "\xff\xff\xff\xff\xff\xff\xff\xff";
    private const NO_BITS_SET = "\x00\x00\x00\x00\x00\x00\x00\x00";
    private const SEED = 'a 16-byte seed!!';

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    #[DataProvider('provideIntSize')]
    public function testGenerateForDateTime(int $intSize): void
    {
        $randomGenerator = $this->mockRandomGenerator();
        $randomGenerator->expects('generate')->with(8)->returns("\xff\x00\xff\x00\xff\x00\xff\x00");

        $unixTimeGenerator = new UnixTimeGenerator($randomGenerator, $intSize);

        $bytes = $unixTimeGenerator->generate(null, null, new DateTimeImmutable('@1578612359.521023'));

        $this->assertSame('016f8ca10161005e3f00ff00ff00ff00', bin2hex($bytes));
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    #[DataProvider('provideIntSize')]
    public function testGenerateForCurrentTime(int $intSize): void
    {
        $this->mockClock(self::CLOCK);

        $randomGenerator = $this->mockRandomGenerator();
        $randomGenerator->expects('generate')->with(8)->returns("\xff\x00\xff\x00\xff\x00\xff\x00");

        $unixTimeGenerator = new UnixTimeGenerator($randomGenerator, $intSize);

        $this->assertSame('016f8ca10161005e3f00ff00ff00ff00', bin2hex($unixTimeGenerator->generate()));
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    #[DataProvider('provideMicrosecondsAndFraction')]
    public function testGenerateStoresFractionOfMillisecond(string $microseconds, string $expectedFraction): void
    {
        $this->mockClock('0.521' . $microseconds . '00 1578612359');
        $unixTimeGenerator = new UnixTimeGenerator(new RandomBytesGenerator());

        $forClock = $unixTimeGenerator->generate();
        $forDateTime = $unixTimeGenerator->generate(
            null,
            null,
            new DateTimeImmutable('@1578612359.521' . $microseconds),
        );

        $this->assertSame('016f8ca10161' . $expectedFraction, bin2hex(substr($forClock, 0, 8)));
        $this->assertSame('016f8ca10161' . $expectedFraction, bin2hex(substr($forDateTime, 0, 8)));
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function provideMicrosecondsAndFraction(): array
    {
        return [
            'no microseconds' => ['000', '0000'],
            'one microsecond' => ['001', '0004'],
            '23 microseconds' => ['023', '005e'],
            'half a millisecond' => ['500', '0800'],
            'the last microsecond' => ['999', '0ffb'],
        ];
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    #[DataProvider('provideIntSize')]
    public function testGenerateIncreasesCounterWhenClockStandsStill(int $intSize): void
    {
        $this->mockClock(self::CLOCK);

        $randomGenerator = $this->mockRandomGenerator();
        $randomGenerator->expects('generate')->with(8)->returns(self::NO_BITS_SET);
        $randomGenerator->expects('generate')->with(16)->returns(self::SEED);

        $unixTimeGenerator = new UnixTimeGenerator($randomGenerator, $intSize);

        $this->assertSame('016f8ca10161005e0000000000000000', bin2hex($unixTimeGenerator->generate()));
        $this->assertSame('016f8ca10161005e00000000003ad87a', bin2hex($unixTimeGenerator->generate()));
        $this->assertSame('016f8ca10161005e0000000000e2de40', bin2hex($unixTimeGenerator->generate()));
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testGenerateKeepsTimestampWhenClockMovesBackward(): void
    {
        $this->mockClock(self::CLOCK, '0.52099900 1578612359', '0.99999900 1578612358');

        $randomGenerator = $this->mockRandomGenerator();
        $randomGenerator->expects('generate')->with(8)->returns(self::NO_BITS_SET);
        $randomGenerator->expects('generate')->with(16)->returns(self::SEED);

        $unixTimeGenerator = new UnixTimeGenerator($randomGenerator);

        $first = $unixTimeGenerator->generate();
        $second = $unixTimeGenerator->generate();
        $third = $unixTimeGenerator->generate();

        $this->assertSame('016f8ca10161005e', bin2hex(substr($second, 0, 8)));
        $this->assertSame('016f8ca10161005e', bin2hex(substr($third, 0, 8)));
        $this->assertTrue($second > $first);
        $this->assertTrue($third > $second);
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testGenerateKeepsFractionWhenClockMovesBackwardWithinMillisecond(): void
    {
        $this->mockClock(self::CLOCK, '0.52100100 1578612359');

        $randomGenerator = $this->mockRandomGenerator();
        $randomGenerator->expects('generate')->with(8)->returns(self::NO_BITS_SET);
        $randomGenerator->expects('generate')->with(16)->returns(self::SEED);

        $unixTimeGenerator = new UnixTimeGenerator($randomGenerator);

        $first = $unixTimeGenerator->generate();
        $second = $unixTimeGenerator->generate();

        $this->assertSame('016f8ca10161005e', bin2hex(substr($second, 0, 8)));
        $this->assertTrue($second > $first);
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testGenerateUsesLaterFractionAndKeepsCounterWithinMillisecond(): void
    {
        $this->mockClock(self::CLOCK, '0.52150000 1578612359');

        $randomGenerator = $this->mockRandomGenerator();
        $randomGenerator->expects('generate')->with(8)->returns(self::NO_BITS_SET);
        $randomGenerator->expects('generate')->with(16)->returns(self::SEED);

        $unixTimeGenerator = new UnixTimeGenerator($randomGenerator);

        $unixTimeGenerator->generate();

        $this->assertSame('016f8ca10161080000000000003ad87a', bin2hex($unixTimeGenerator->generate()));
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testGenerateStartsNewCounterInLaterMillisecond(): void
    {
        $this->mockClock(self::CLOCK, '0.52200000 1578612359');

        $randomGenerator = $this->mockRandomGenerator();
        $randomGenerator->expects('generate')->with(8)->times(2)->returns(self::ALL_BITS_SET, self::NO_BITS_SET);

        $unixTimeGenerator = new UnixTimeGenerator($randomGenerator);

        $this->assertSame('016f8ca10161005e3fffffffffffffff', bin2hex($unixTimeGenerator->generate()));
        $this->assertSame('016f8ca1016200000000000000000000', bin2hex($unixTimeGenerator->generate()));
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    #[DataProvider('provideCounterAndIncreasedCounter')]
    public function testGenerateAddsToBothHalvesOfCounter(string $counter, string $expectedCounter, int $intSize): void
    {
        $this->mockClock(self::CLOCK);

        $randomGenerator = $this->mockRandomGenerator();
        $randomGenerator->expects('generate')->with(8)->returns((string) hex2bin($counter));
        $randomGenerator->expects('generate')->with(16)->returns(self::SEED);

        $unixTimeGenerator = new UnixTimeGenerator($randomGenerator, $intSize);

        $this->assertSame('016f8ca10161005e' . $counter, bin2hex($unixTimeGenerator->generate()));
        $this->assertSame('016f8ca10161005e' . $expectedCounter, bin2hex($unixTimeGenerator->generate()));
    }

    /**
     * The seed makes the first increase 0x3ad87a, and the second half of the counter holds 31 bits.
     *
     * @return array<string, array{string, string, int}>
     */
    public static function provideCounterAndIncreasedCounter(): array
    {
        $cases = [
            'the increase fills the second half' => ['00000002ffc52785', '00000002ffffffff'],
            'the increase passes the second half by one' => ['00000002ffc52786', '0000000300000000'],
            'the second half is full' => ['00000002ffffffff', '00000003003ad879'],
            'the increase fills the whole counter' => ['3fffffffffc52785', '3fffffffffffffff'],
        ];

        $provided = [];

        foreach ($cases as $name => $case) {
            $provided[$name . ', 64-bit'] = [$case[0], $case[1], 8];
            $provided[$name . ', 32-bit'] = [$case[0], $case[1], 4];
        }

        return $provided;
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    #[DataProvider('provideIntSize')]
    public function testGenerateMovesToNextFractionWhenIncreasePassesWholeCounterByOne(int $intSize): void
    {
        $this->mockClock(self::CLOCK);

        $randomGenerator = $this->mockRandomGenerator();
        $randomGenerator->expects('generate')->with(8)->times(2)->returns(
            (string) hex2bin('3fffffffffc52786'),
            "\x00\x00\x00\x00\x00\x00\x00\x01",
        );
        $randomGenerator->expects('generate')->with(16)->returns(self::SEED);

        $unixTimeGenerator = new UnixTimeGenerator($randomGenerator, $intSize);

        $this->assertSame('016f8ca10161005e3fffffffffc52786', bin2hex($unixTimeGenerator->generate()));
        $this->assertSame('016f8ca10161005f0000000000000001', bin2hex($unixTimeGenerator->generate()));
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    #[DataProvider('provideIntSize')]
    public function testGenerateKeepsOrderInFirstSecondOfUnixEpoch(int $intSize): void
    {
        $this->mockClock('0.09999900 0');

        $randomGenerator = $this->mockRandomGenerator();
        $randomGenerator->allows('generate')->with(8)->returns(self::ALL_BITS_SET);
        $randomGenerator->expects('generate')->with(16)->returns(self::SEED);

        $unixTimeGenerator = new UnixTimeGenerator($randomGenerator, $intSize);

        // The last microsecond of a millisecond has the fraction 0xffb, so four more fractions follow it.
        foreach (['0ffb', '0ffc', '0ffd', '0ffe', '0fff'] as $fraction) {
            $this->assertSame(
                '000000000063' . $fraction . '3fffffffffffffff',
                bin2hex($unixTimeGenerator->generate()),
            );
        }

        $this->assertSame('00000000006400003fffffffffffffff', bin2hex($unixTimeGenerator->generate()));
        $this->assertSame('00000000006400013fffffffffffffff', bin2hex($unixTimeGenerator->generate()));
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    #[DataProvider('provideIntSize')]
    public function testGenerateThrowsExceptionWhenClockIsBeforeUnixEpoch(int $intSize): void
    {
        $this->mockClock('0.99900000 -1');

        $unixTimeGenerator = new UnixTimeGenerator($this->mockRandomGenerator(), $intSize);

        $this->expectException(TimeSourceException::class);
        $this->expectExceptionMessage(
            'The current time is before 1970-01-01 00:00:00 UTC, the first time a version 7 UUID can have',
        );

        $unixTimeGenerator->generate();
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    #[DataProvider('provideIntSize')]
    public function testGenerateMovesToNextFractionWhenCounterIsFull(int $intSize): void
    {
        $this->mockClock(self::CLOCK);

        $randomGenerator = $this->mockRandomGenerator();
        $randomGenerator->expects('generate')->with(8)->times(2)->returns(
            self::ALL_BITS_SET,
            "\x00\x00\x00\x00\x00\x00\x00\x01",
        );
        $randomGenerator->expects('generate')->with(16)->returns(self::SEED);

        $unixTimeGenerator = new UnixTimeGenerator($randomGenerator, $intSize);

        $this->assertSame('016f8ca10161005e3fffffffffffffff', bin2hex($unixTimeGenerator->generate()));
        $this->assertSame('016f8ca10161005f0000000000000001', bin2hex($unixTimeGenerator->generate()));
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    #[DataProvider('provideDateTimeAndNextMillisecond')]
    public function testGenerateMovesToNextMillisecondWhenFractionIsFull(
        string $dateTime,
        string $expectedTimeHex,
        string $expectedNextTimeHex,
        int $intSize,
    ): void {
        $randomGenerator = $this->mockRandomGenerator();
        $randomGenerator->allows('generate')->with(8)->returns(self::ALL_BITS_SET);
        $randomGenerator->expects('generate')->with(16)->returns(self::SEED);

        $unixTimeGenerator = new UnixTimeGenerator($randomGenerator, $intSize);
        $dateTime = new DateTimeImmutable($dateTime);

        // The last microsecond of a millisecond has the fraction 0xffb, so four more fractions follow it.
        foreach (['0ffb', '0ffc', '0ffd', '0ffe', '0fff'] as $fraction) {
            $this->assertSame(
                $expectedTimeHex . $fraction . '3fffffffffffffff',
                bin2hex($unixTimeGenerator->generate(null, null, $dateTime)),
            );
        }

        $this->assertSame(
            $expectedNextTimeHex . '0000' . '3fffffffffffffff',
            bin2hex($unixTimeGenerator->generate(null, null, $dateTime)),
        );
    }

    /**
     * @return array<string, array{string, string, string, int}>
     */
    public static function provideDateTimeAndNextMillisecond(): array
    {
        $cases = [
            'the Unix Epoch' => ['@0.000999', '000000000000', '000000000001'],
            'nine digits of milliseconds' => ['@999999.999999', '00003b9ac9ff', '00003b9aca00'],
            'thirteen digits of milliseconds' => ['@1578612359.521999', '016f8ca10161', '016f8ca10162'],
            'nine nines at the end' => ['@1999999999.999999', '01d1a94a1fff', '01d1a94a2000'],
        ];

        $provided = [];

        foreach ($cases as $name => $case) {
            $provided[$name . ', 64-bit'] = [$case[0], $case[1], $case[2], 8];
            $provided[$name . ', 32-bit'] = [$case[0], $case[1], $case[2], 4];
        }

        return $provided;
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    #[DataProvider('provideIntSize')]
    public function testGenerateThrowsExceptionWhenTimestampWouldPassLastMillisecond(int $intSize): void
    {
        $randomGenerator = $this->mockRandomGenerator();
        $randomGenerator->allows('generate')->with(8)->returns(self::ALL_BITS_SET);
        $randomGenerator->expects('generate')->with(16)->returns(self::SEED);

        $unixTimeGenerator = new UnixTimeGenerator($randomGenerator, $intSize);
        $dateTime = new DateTimeImmutable('@281474976710.655999');

        for ($i = 0; $i < 5; $i++) {
            $unixTimeGenerator->generate(null, null, $dateTime);
        }

        $this->expectException(TimeSourceException::class);
        $this->expectExceptionMessage('The timestamp of a version 7 UUID cannot go past 10889-08-02 05:31:50.655 UTC');

        $unixTimeGenerator->generate(null, null, $dateTime);
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testGenerateAcceptsLastMillisecondOfClock(): void
    {
        $this->mockClock('0.65599900 281474976710');

        $randomGenerator = $this->mockRandomGenerator();
        $randomGenerator->expects('generate')->with(8)->returns(self::NO_BITS_SET);

        $unixTimeGenerator = new UnixTimeGenerator($randomGenerator);

        $this->assertSame('ffffffffffff0ffb0000000000000000', bin2hex($unixTimeGenerator->generate()));
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    #[DataProvider('provideClockAfterLastMillisecond')]
    public function testGenerateThrowsExceptionWhenClockIsAfterLastMillisecond(string $clock): void
    {
        $this->mockClock($clock);

        $unixTimeGenerator = new UnixTimeGenerator($this->mockRandomGenerator());

        $this->expectException(TimeSourceException::class);
        $this->expectExceptionMessage(
            'The current time is after 10889-08-02 05:31:50.655 UTC, the last time a version 7 UUID can have',
        );

        $unixTimeGenerator->generate();
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideClockAfterLastMillisecond(): array
    {
        return [
            'one millisecond after the last time' => ['0.65600000 281474976710'],
            'a thousand years after the last time' => ['0.00000000 312000000000'],
            'one more digit of seconds' => ['0.00000000 1000000000000'],
        ];
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testGenerateForDateTimeLeavesCurrentTimeAlone(): void
    {
        $this->mockClock(self::CLOCK);

        $randomGenerator = $this->mockRandomGenerator();
        $randomGenerator->expects('generate')->with(8)->times(2)->returns(self::NO_BITS_SET, self::ALL_BITS_SET);
        $randomGenerator->expects('generate')->with(16)->returns(self::SEED);

        $unixTimeGenerator = new UnixTimeGenerator($randomGenerator);

        $unixTimeGenerator->generate();
        $future = $unixTimeGenerator->generate(null, null, new DateTimeImmutable('3000-01-01T00:00:00+00:00'));

        $this->assertSame('1d8fda4ce00000003fffffffffffffff', bin2hex($future));
        $this->assertSame('016f8ca10161005e00000000003ad87a', bin2hex($unixTimeGenerator->generate()));
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testGenerateForCurrentTimeLeavesDateTimeCounterAlone(): void
    {
        $this->mockClock(self::CLOCK);

        $randomGenerator = $this->mockRandomGenerator();
        $randomGenerator->expects('generate')->with(8)->times(2)->returns(self::NO_BITS_SET, self::ALL_BITS_SET);
        $randomGenerator->expects('generate')->with(16)->returns(self::SEED);

        $unixTimeGenerator = new UnixTimeGenerator($randomGenerator);
        $dateTime = new DateTimeImmutable('3000-01-01T00:00:00+00:00');

        $unixTimeGenerator->generate(null, null, $dateTime);
        $unixTimeGenerator->generate();

        $this->assertSame(
            '1d8fda4ce000000000000000003ad87a',
            bin2hex($unixTimeGenerator->generate(null, null, $dateTime)),
        );
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testGenerateStartsNewCounterForDifferentDateTime(): void
    {
        $randomGenerator = $this->mockRandomGenerator();
        $randomGenerator->expects('generate')->with(8)->times(3)->returns(
            self::ALL_BITS_SET,
            self::NO_BITS_SET,
            "\x00\x00\x00\x00\x00\x00\x00\x01",
        );

        $unixTimeGenerator = new UnixTimeGenerator($randomGenerator);
        $later = new DateTimeImmutable('@1578612359.521023');
        $earlier = new DateTimeImmutable('@1578612359.521022');

        $this->assertSame(
            '016f8ca10161005e3fffffffffffffff',
            bin2hex($unixTimeGenerator->generate(null, null, $later)),
        );
        $this->assertSame(
            '016f8ca10161005a0000000000000000',
            bin2hex($unixTimeGenerator->generate(null, null, $earlier)),
        );
        $this->assertSame(
            '016f8ca10161005e0000000000000001',
            bin2hex($unixTimeGenerator->generate(null, null, $later)),
        );
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    #[DataProvider('provideIntSize')]
    public function testGenerateDrawsNewSeedAndMovesToNextFractionAfterFork(int $intSize): void
    {
        $this->mockClock(self::CLOCK);
        $this->mockProcessId(100, 100, 200);

        $randomGenerator = $this->mockRandomGenerator();
        $randomGenerator->expects('generate')->with(8)->times(2)->returns(
            self::NO_BITS_SET,
            "\x00\x00\x00\x00\x00\x00\x00\x01",
        );
        $randomGenerator->expects('generate')->with(16)->times(2)->returns(self::SEED, 'another seed 16b');

        $unixTimeGenerator = new UnixTimeGenerator($randomGenerator, $intSize);

        $unixTimeGenerator->generate();
        $this->assertSame('016f8ca10161005e00000000003ad87a', bin2hex($unixTimeGenerator->generate()));

        // The process identifier changes here. With the same seed, the next counter would end in e2de40.
        $this->assertSame('016f8ca10161005f0000000000000001', bin2hex($unixTimeGenerator->generate()));
        $this->assertSame('016f8ca10161005f0000000000606a94', bin2hex($unixTimeGenerator->generate()));
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testGenerateMovesEachSequenceToNextFractionAfterFork(): void
    {
        $this->mockClock(self::CLOCK);
        $this->mockProcessId(100, 100, 200);

        $randomGenerator = $this->mockRandomGenerator();
        $randomGenerator->allows('generate')->with(8)->returns(self::NO_BITS_SET);

        $unixTimeGenerator = new UnixTimeGenerator($randomGenerator);
        $dateTime = new DateTimeImmutable('@1578612359.521023');

        $unixTimeGenerator->generate(null, null, $dateTime);
        $unixTimeGenerator->generate();

        // The process identifier changes here.
        $this->assertSame('016f8ca10161005f0000000000000000', bin2hex($unixTimeGenerator->generate()));
        $this->assertSame(
            '016f8ca10161005f0000000000000000',
            bin2hex($unixTimeGenerator->generate(null, null, $dateTime)),
        );
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testGenerateMovesEachSequenceToNextFractionAfterForkWhenDateTimeComesFirst(): void
    {
        $this->mockClock(self::CLOCK);
        $this->mockProcessId(100, 100, 200);

        $randomGenerator = $this->mockRandomGenerator();
        $randomGenerator->allows('generate')->with(8)->returns(self::NO_BITS_SET);

        $unixTimeGenerator = new UnixTimeGenerator($randomGenerator);
        $dateTime = new DateTimeImmutable('@1578612359.521023');

        $unixTimeGenerator->generate();
        $unixTimeGenerator->generate(null, null, $dateTime);

        // The process identifier changes here.
        $this->assertSame(
            '016f8ca10161005f0000000000000000',
            bin2hex($unixTimeGenerator->generate(null, null, $dateTime)),
        );
        $this->assertSame('016f8ca10161005f0000000000000000', bin2hex($unixTimeGenerator->generate()));
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testGenerateWorksWhenProcessIdentifierCannotBeRead(): void
    {
        $this->mockClock(self::CLOCK);
        $this->stubFunction(
            'Ramsey\Uuid\Generator',
            'getmypid',
            fn () => $this->fail('Expected no call to getmypid()'),
        );

        $randomGenerator = $this->mockRandomGenerator();
        $randomGenerator->expects('generate')->with(8)->returns(self::NO_BITS_SET);
        $randomGenerator->expects('generate')->with(16)->returns(self::SEED);

        $unixTimeGenerator = new UnixTimeGenerator($randomGenerator);

        Closure::bind(
            static function (): void {
                self::$readsProcessId = false;
            },
            null,
            UnixTimeGenerator::class,
        )();

        $this->assertSame('016f8ca10161005e0000000000000000', bin2hex($unixTimeGenerator->generate()));
        $this->assertSame('016f8ca10161005e00000000003ad87a', bin2hex($unixTimeGenerator->generate()));
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testGeneratorsOfBothIntegerSizesShareOneSequence(): void
    {
        $this->mockClock(self::CLOCK);

        $randomGenerator = $this->mockRandomGenerator();
        $randomGenerator->expects('generate')->with(8)->returns(self::NO_BITS_SET);
        $randomGenerator->expects('generate')->with(16)->returns(self::SEED);

        $generator64 = new UnixTimeGenerator($randomGenerator, 8);
        $generator32 = new UnixTimeGenerator($randomGenerator, 4);

        $this->assertSame('016f8ca10161005e0000000000000000', bin2hex($generator64->generate()));
        $this->assertSame('016f8ca10161005e00000000003ad87a', bin2hex($generator32->generate()));
        $this->assertSame('016f8ca10161005e0000000000e2de40', bin2hex($generator64->generate()));
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    #[DataProvider('provideWrongLength')]
    public function testGenerateThrowsExceptionWhenRandomGeneratorReturnsWrongLengthForCounter(string $bytes): void
    {
        $randomGenerator = $this->mockRandomGenerator();
        $randomGenerator->expects('generate')->with(8)->returns($bytes);

        $unixTimeGenerator = new UnixTimeGenerator($randomGenerator);

        $this->expectException(RandomSourceException::class);
        $this->expectExceptionMessage(
            'The random generator must return 8 bytes; received ' . strlen($bytes) . ' bytes',
        );

        $unixTimeGenerator->generate();
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    #[DataProvider('provideWrongLength')]
    public function testGenerateThrowsExceptionWhenRandomGeneratorReturnsWrongLengthForSeed(string $bytes): void
    {
        $this->mockClock(self::CLOCK);

        $randomGenerator = $this->mockRandomGenerator();
        $randomGenerator->expects('generate')->with(8)->returns(self::NO_BITS_SET);
        $randomGenerator->expects('generate')->with(16)->returns($bytes);

        $unixTimeGenerator = new UnixTimeGenerator($randomGenerator);
        $unixTimeGenerator->generate();

        $this->expectException(RandomSourceException::class);
        $this->expectExceptionMessage(
            'The random generator must return 16 bytes; received ' . strlen($bytes) . ' bytes',
        );

        $unixTimeGenerator->generate();
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideWrongLength(): array
    {
        return [
            'no bytes' => [''],
            'seven bytes' => ['1234567'],
            'nine bytes' => ['123456789'],
            'seventeen bytes' => ['12345678901234567'],
        ];
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    #[DataProvider('provideIntSize')]
    public function testGenerateProducesMonotonicResults(int $intSize): void
    {
        $unixTimeGenerator = new UnixTimeGenerator(new RandomBytesGenerator(), $intSize);

        $previous = '';
        for ($i = 0; $i < self::ITERATIONS; $i++) {
            $bytes = $unixTimeGenerator->generate();
            $this->assertTrue(
                $bytes > $previous,
                'Failed on iteration ' . $i . ' when evaluating ' . bin2hex($bytes) . ' > ' . bin2hex($previous),
            );
            $previous = $bytes;
        }
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    #[DataProvider('provideIntSize')]
    public function testGenerateProducesMonotonicResultsWithSameDate(int $intSize): void
    {
        $dateTime = new DateTimeImmutable('now');
        $unixTimeGenerator = new UnixTimeGenerator(new RandomBytesGenerator(), $intSize);

        $previous = '';
        for ($i = 0; $i < self::ITERATIONS; $i++) {
            $bytes = $unixTimeGenerator->generate(null, null, $dateTime);
            $this->assertTrue(
                $bytes > $previous,
                'Failed on iteration ' . $i . ' when evaluating ' . bin2hex($bytes) . ' > ' . bin2hex($previous),
            );
            $previous = $bytes;
        }
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    #[DataProvider('provideIntSize')]
    public function testGenerateProducesMonotonicResultsStartingWithAllBitsSet(int $intSize): void
    {
        $randomGenerator = $this->mockRandomGenerator();
        $randomGenerator->allows('generate')->with(8)->returns(self::ALL_BITS_SET);
        $randomGenerator->expects('generate')->with(16)->returns(str_repeat("\xff", 16));

        $unixTimeGenerator = new UnixTimeGenerator($randomGenerator, $intSize);

        $previous = '';
        for ($i = 0; $i < self::ITERATIONS; $i++) {
            $bytes = $unixTimeGenerator->generate();
            $this->assertTrue(
                $bytes > $previous,
                'Failed on iteration ' . $i . ' when evaluating ' . bin2hex($bytes) . ' > ' . bin2hex($previous),
            );
            $previous = $bytes;
        }
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    #[DataProvider('provideIntSize')]
    public function testGenerateProducesMonotonicResultsWithSameDateStartingWithAllBitsSet(int $intSize): void
    {
        $dateTime = new DateTimeImmutable('now');

        $randomGenerator = $this->mockRandomGenerator();
        $randomGenerator->allows('generate')->with(8)->returns(self::ALL_BITS_SET);
        $randomGenerator->expects('generate')->with(16)->returns(str_repeat("\xff", 16));

        $unixTimeGenerator = new UnixTimeGenerator($randomGenerator, $intSize);

        $previous = '';
        for ($i = 0; $i < self::ITERATIONS; $i++) {
            $bytes = $unixTimeGenerator->generate(null, null, $dateTime);
            $this->assertTrue(
                $bytes > $previous,
                'Failed on iteration ' . $i . ' when evaluating ' . bin2hex($bytes) . ' > ' . bin2hex($previous),
            );
            $previous = $bytes;
        }
    }

    #[DataProvider('provideDateTimeOutOfRange')]
    public function testGenerateThrowsExceptionForDateTimeOutOfRange(string $dateTime): void
    {
        $unixTimeGenerator = new UnixTimeGenerator(new RandomBytesGenerator());

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The date-time for a version 7 UUID must be from 1970-01-01 00:00:00 UTC '
            . 'through 10889-08-02 05:31:50.655 UTC',
        );

        $unixTimeGenerator->generate(null, null, new DateTimeImmutable($dateTime));
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideDateTimeOutOfRange(): array
    {
        return [
            'one millisecond before the Unix Epoch' => ['1969-12-31T23:59:59.999+00:00'],
            'ten years before the Unix Epoch' => ['1960-01-01T00:00:00+00:00'],
            'one millisecond after the last time' => ['@281474976710.656'],
            'a thousand years after the last time' => ['@312000000000'],
        ];
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    #[DataProvider('provideDateTimeAtEdgeOfRange')]
    public function testGenerateAcceptsDateTimeAtEdgeOfRange(string $dateTime, string $expectedTimeHex): void
    {
        $unixTimeGenerator = new UnixTimeGenerator(new RandomBytesGenerator());

        $bytes = $unixTimeGenerator->generate(null, null, new DateTimeImmutable($dateTime));

        $this->assertSame($expectedTimeHex, bin2hex(substr($bytes, 0, 6)));
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function provideDateTimeAtEdgeOfRange(): array
    {
        return [
            'the Unix Epoch' => ['1970-01-01T00:00:00+00:00', '000000000000'],
            'the last time' => ['@281474976710.655', 'ffffffffffff'],
        ];
    }

    /**
     * @return array<string, array{int}>
     */
    public static function provideIntSize(): array
    {
        return [
            '64-bit' => [8],
            '32-bit' => [4],
        ];
    }

    /**
     * Makes the generator read the given values from the clock, one for each call, and the last one from then on
     */
    private function mockClock(string ...$values): void
    {
        $this->stubFunction(
            'Ramsey\Uuid\Generator',
            'microtime',
            static function () use (&$values): string {
                return count($values) > 1 ? array_shift($values) : $values[0];
            },
        );
    }

    /**
     * Makes the generator read the given process identifiers, one for each call, and the last one from then on
     */
    private function mockProcessId(int ...$values): void
    {
        $this->stubFunction(
            'Ramsey\Uuid\Generator',
            'getmypid',
            static function () use (&$values): int {
                return count($values) > 1 ? array_shift($values) : $values[0];
            },
        );
    }

    /**
     * @return RandomGeneratorInterface & DoubleInterface
     */
    private function mockRandomGenerator(): RandomGeneratorInterface
    {
        return Double::for(RandomGeneratorInterface::class);
    }
}
