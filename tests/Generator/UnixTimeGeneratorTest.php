<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Generator;

use DateTimeImmutable;
use Mockery;
use Mockery\MockInterface;
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
}
