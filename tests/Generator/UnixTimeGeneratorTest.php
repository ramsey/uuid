<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Generator;

use Mockery;
use Mockery\MockInterface;
use Ramsey\Uuid\Converter\Time\UnixTimeConverter;
use Ramsey\Uuid\Generator\RandomGeneratorInterface;
use Ramsey\Uuid\Generator\UnixTimeGenerator;
use Ramsey\Uuid\Math\BrickMathCalculator;
use Ramsey\Uuid\Provider\TimeProviderInterface;
use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\Type\Time;

class UnixTimeGeneratorTest extends TestCase
{
    public function testGenerate(): void
    {
        $unixTimeConverter = new UnixTimeConverter(new BrickMathCalculator());

        /** @var TimeProviderInterface&MockInterface $timeProvider */
        $timeProvider = Mockery::mock(TimeProviderInterface::class, [
            'getTime' => new Time('1578612359', '521023'),
        ]);

        /** @var RandomGeneratorInterface&MockInterface $randomGenerator */
        $randomGenerator = Mockery::mock(RandomGeneratorInterface::class);
        $randomGenerator->expects()->generate(10)->andReturns("\xff\x00\xff\x00\xff\x00\xff\x00\xff\x00");

        $unixTimeGenerator = new UnixTimeGenerator($unixTimeConverter, $timeProvider, $randomGenerator);

        $this->assertSame(
            "\x01\x6f\x8c\xa1\x01\x61\xff\x00\xff\x00\xff\x00\xff\x00\xff\x00",
            $unixTimeGenerator->generate(),
        );
    }
}
