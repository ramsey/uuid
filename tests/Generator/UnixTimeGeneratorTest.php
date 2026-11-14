<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Generator;

use DateTimeImmutable;
use Mockery;
use Mockery\MockInterface;
use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Generator\RandomBytesGenerator;
use Ramsey\Uuid\Generator\RandomGeneratorInterface;
use Ramsey\Uuid\Generator\UnixTimeGenerator;
use Ramsey\Uuid\Test\TestCase;

class UnixTimeGeneratorTest extends TestCase
{
    private const ITERATIONS = 2000;

    /**
     * @runInSeparateProcess since values are stored statically on the class
     * @preserveGlobalState disabled
     */
    public function testGenerate(): void
    {
        $dateTime = new DateTimeImmutable('@1578612359.521023');
        $expectedBytes = "\x01\x6f\x8c\xa1\x01\x61\x03\x00\xff\x00\xff\x00\xff\x00\xff\x00";

        /** @var RandomGeneratorInterface&MockInterface $randomGenerator */
        $randomGenerator = Mockery::mock(RandomGeneratorInterface::class);
        $randomGenerator->expects()->generate(16)->andReturns(
            "\xff\x00\xff\x00\xff\x00\xff\x00\xff\x00\xff\x00\xff\x00\xff\x00",
        );

        $unixTimeGenerator = new UnixTimeGenerator($randomGenerator);

        $bytes = $unixTimeGenerator->generate(null, null, $dateTime);

        $this->assertSame(
            $expectedBytes,
            $bytes,
            'Failed asserting that "' . bin2hex($bytes) . '" is equal to "' . bin2hex($expectedBytes) . '"',
        );
    }

    /**
     * @runInSeparateProcess since values are stored statically on the class
     * @preserveGlobalState disabled
     */
    public function testGenerateProducesMonotonicResults(): void
    {
        $randomGenerator = new RandomBytesGenerator();
        $unixTimeGenerator = new UnixTimeGenerator($randomGenerator);

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

    /**
     * @runInSeparateProcess since values are stored statically on the class
     * @preserveGlobalState disabled
     */
    public function testGenerateProducesMonotonicResultsWithSameDate(): void
    {
        $dateTime = new DateTimeImmutable('now');
        $randomGenerator = new RandomBytesGenerator();
        $unixTimeGenerator = new UnixTimeGenerator($randomGenerator);

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

    /**
     * @runInSeparateProcess since values are stored statically on the class
     * @preserveGlobalState disabled
     */
    public function testGenerateProducesMonotonicResultsFor32BitPath(): void
    {
        $randomGenerator = new RandomBytesGenerator();
        $unixTimeGenerator = new UnixTimeGenerator($randomGenerator, 4);

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

    /**
     * @runInSeparateProcess since values are stored statically on the class
     * @preserveGlobalState disabled
     */
    public function testGenerateProducesMonotonicResultsWithSameDateFor32BitPath(): void
    {
        $dateTime = new DateTimeImmutable('now');
        $randomGenerator = new RandomBytesGenerator();
        $unixTimeGenerator = new UnixTimeGenerator($randomGenerator, 4);

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

    /**
     * @runInSeparateProcess since values are stored statically on the class
     * @preserveGlobalState disabled
     */
    public function testGenerateProducesMonotonicResultsStartingWithAllBitsSet(): void
    {
        /** @var RandomGeneratorInterface&MockInterface $randomGenerator */
        $randomGenerator = Mockery::mock(RandomGeneratorInterface::class);
        $randomGenerator->expects()->generate(16)->andReturns(
            "\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff",
        );
        $randomGenerator->allows()->generate(10)->andReturns(
            "\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff",
        );

        $unixTimeGenerator = new UnixTimeGenerator($randomGenerator);

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

    /**
     * @runInSeparateProcess since values are stored statically on the class
     * @preserveGlobalState disabled
     */
    public function testGenerateRollsOverWithAllBitsSetWithSameDate(): void
    {
        $dateTime = new DateTimeImmutable('now');

        /** @var RandomGeneratorInterface&MockInterface $randomGenerator */
        $randomGenerator = Mockery::mock(RandomGeneratorInterface::class);
        $randomGenerator->expects()->generate(16)->andReturns(
            "\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff",
        );
        $randomGenerator->allows()->generate(10)->andReturns(
            "\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff",
        );

        $unixTimeGenerator = new UnixTimeGenerator($randomGenerator);

        // We can only call this twice before the overflow kicks in, "randomizing" all the bits back to 1's, according to
        // our mocked random generator. As a result, we can't run this in a loop like with the other monotonicity tests
        // in this class; it starts failing at the third loop. This is okay, since our goal is to test the overflow.
        $first = $unixTimeGenerator->generate(null, null, $dateTime);
        $second = $unixTimeGenerator->generate(null, null, $dateTime);

        $this->assertTrue($second > $first);
    }

    /**
     * @runInSeparateProcess since values are stored statically on the class
     * @preserveGlobalState disabled
     */
    public function testGenerateProducesMonotonicResultsStartingWithAllBitsSetFor32BitPath(): void
    {
        /** @var RandomGeneratorInterface&MockInterface $randomGenerator */
        $randomGenerator = Mockery::mock(RandomGeneratorInterface::class);
        $randomGenerator->expects()->generate(16)->andReturns(
            "\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff",
        );
        $randomGenerator->allows()->generate(10)->andReturns(
            "\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff",
        );

        $unixTimeGenerator = new UnixTimeGenerator($randomGenerator, 4);

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

    /**
     * @runInSeparateProcess since values are stored statically on the class
     * @preserveGlobalState disabled
     */
    public function testGenerateRollsOverWithAllBitsSetWithSameDateFor32BitPath(): void
    {
        $dateTime = new DateTimeImmutable('now');

        /** @var RandomGeneratorInterface&MockInterface $randomGenerator */
        $randomGenerator = Mockery::mock(RandomGeneratorInterface::class);
        $randomGenerator->expects()->generate(16)->andReturns(
            "\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff",
        );
        $randomGenerator->allows()->generate(10)->andReturns(
            "\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff",
        );

        $unixTimeGenerator = new UnixTimeGenerator($randomGenerator, 4);

        // We can only call this twice before the overflow kicks in, "randomizing" all the bits back to 1's, according to
        // our mocked random generator. As a result, we can't run this in a loop like with the other monotonicity tests
        // in this class; it starts failing at the third loop. This is okay, since our goal is to test the overflow.
        $first = $unixTimeGenerator->generate(null, null, $dateTime);
        $second = $unixTimeGenerator->generate(null, null, $dateTime);

        $this->assertTrue($second > $first);
    }

    /**
     * @dataProvider provideDateTimeOutOfRange
     */
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
    public function provideDateTimeOutOfRange(): array
    {
        return [
            'one millisecond before the Unix Epoch' => ['1969-12-31T23:59:59.999+00:00'],
            'ten years before the Unix Epoch' => ['1960-01-01T00:00:00+00:00'],
            'one millisecond after the last time' => ['@281474976710.656'],
            'a thousand years after the last time' => ['@312000000000'],
        ];
    }

    /**
     * @dataProvider provideDateTimeAtEdgeOfRange
     */
    public function testGenerateAcceptsDateTimeAtEdgeOfRange(string $dateTime, string $expectedTimeHex): void
    {
        $unixTimeGenerator = new UnixTimeGenerator(new RandomBytesGenerator());

        $bytes = $unixTimeGenerator->generate(null, null, new DateTimeImmutable($dateTime));

        $this->assertSame($expectedTimeHex, bin2hex(substr($bytes, 0, 6)));
    }

    /**
     * @return array<string, array{string, string}>
     */
    public function provideDateTimeAtEdgeOfRange(): array
    {
        return [
            'the Unix Epoch' => ['1970-01-01T00:00:00+00:00', '000000000000'],
            'the last time' => ['@281474976710.655', 'ffffffffffff'],
        ];
    }
}
