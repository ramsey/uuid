<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Generator;

use Mockery;
use Ramsey\Uuid\Converter\Number\GenericNumberConverter;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Converter\Time\GenericTimeConverter;
use Ramsey\Uuid\Exception\DceSecurityException;
use Ramsey\Uuid\Generator\DceSecurityGenerator;
use Ramsey\Uuid\Generator\DefaultTimeGenerator;
use Ramsey\Uuid\Generator\TimeGeneratorInterface;
use Ramsey\Uuid\Math\BrickMathCalculator;
use Ramsey\Uuid\Provider\DceSecurityProviderInterface;
use Ramsey\Uuid\Provider\NodeProviderInterface;
use Ramsey\Uuid\Provider\Time\FixedTimeProvider;
use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Type\Integer as IntegerObject;
use Ramsey\Uuid\Type\Time;
use Ramsey\Uuid\Uuid;

use function bin2hex;
use function substr;

class DceSecurityGeneratorTest extends TestCase
{
    /**
     * @param int|string $uid
     * @param int|string $gid
     * @param int|string $seconds
     * @param int|string $microseconds
     *
     * @dataProvider provideValuesForDceSecurityGenerator
     */
    public function testGenerateBytesReplacesBytesWithDceValues(
        $uid,
        $gid,
        string $node,
        $seconds,
        $microseconds,
        int $providedDomain,
        ?IntegerObject $providedId,
        ?Hexadecimal $providedNode,
        string $expectedId,
        string $expectedDomain,
        string $expectedNode,
        string $expectedTimeMidHi
    ): void {
        /** @var DceSecurityProviderInterface $dceSecurityProvider */
        $dceSecurityProvider = Mockery::mock(DceSecurityProviderInterface::class, [
            'getUid' => new IntegerObject($uid),
            'getGid' => new IntegerObject($gid),
        ]);

        /** @var NodeProviderInterface $nodeProvider */
        $nodeProvider = Mockery::mock(NodeProviderInterface::class, [
            'getNode' => new Hexadecimal($node),
        ]);

        $timeProvider = new FixedTimeProvider(new Time($seconds, $microseconds));

        $calculator = new BrickMathCalculator();
        $numberConverter = new GenericNumberConverter($calculator);
        $timeConverter = new GenericTimeConverter($calculator);
        $timeGenerator = new DefaultTimeGenerator($nodeProvider, $timeConverter, $timeProvider);

        $dceSecurityGenerator = new DceSecurityGenerator($numberConverter, $timeGenerator, $dceSecurityProvider);

        $bytes = $dceSecurityGenerator->generate($providedDomain, $providedId, $providedNode);

        $this->assertSame($expectedId, bin2hex(substr($bytes, 0, 4)));
        $this->assertSame($expectedDomain, bin2hex(substr($bytes, 9, 1)));
        $this->assertSame($expectedNode, bin2hex(substr($bytes, 10)));
        $this->assertSame($expectedTimeMidHi, bin2hex(substr($bytes, 4, 4)));
    }

    /**
     * @return array<array{uid: int|string, node: string, seconds: int, microseconds: int, providedDomain: int, providedId: IntegerObject|null, providedNode: null, expectedId: string, expectedDomain: string, expectedNode: string, expectedTimeMidHi: string}>
     */
    public function provideValuesForDceSecurityGenerator(): array
    {
        return [
            [
                'uid' => '1001',
                'gid' => '2001',
                'node' => '001122334455',
                'seconds' => 1579132397,
                'microseconds' => 500000,
                'providedDomain' => Uuid::DCE_DOMAIN_PERSON,
                'providedId' => null,
                'providedNode' => null,
                'expectedId' => '000003e9',
                'expectedDomain' => '00',
                'expectedNode' => '001122334455',
                'expectedTimeMidHi' => '37f201ea',
            ],
            [
                'uid' => '1001',
                'gid' => '2001',
                'node' => '001122334455',
                'seconds' => 1579132397,
                'microseconds' => 500000,
                'providedDomain' => Uuid::DCE_DOMAIN_GROUP,
                'providedId' => null,
                'providedNode' => null,
                'expectedId' => '000007d1',
                'expectedDomain' => '01',
                'expectedNode' => '001122334455',
                'expectedTimeMidHi' => '37f201ea',
            ],
            [
                'uid' => 0,
                'gid' => 0,
                'node' => '001122334455',
                'seconds' => 1579132397,
                'microseconds' => 500000,
                'providedDomain' => Uuid::DCE_DOMAIN_ORG,
                'providedId' => new IntegerObject('4294967295'),
                'providedNode' => null,
                'expectedId' => 'ffffffff',
                'expectedDomain' => '02',
                'expectedNode' => '001122334455',
                'expectedTimeMidHi' => '37f201ea',
            ],
        ];
    }

    public function testGenerateThrowsExceptionForInvalidDomain(): void
    {
        $numberConverter = Mockery::mock(NumberConverterInterface::class);
        $timeGenerator = Mockery::mock(TimeGeneratorInterface::class);
        $dceSecurityProvider = Mockery::mock(DceSecurityProviderInterface::class);

        $generator = new DceSecurityGenerator($numberConverter, $timeGenerator, $dceSecurityProvider);

        $this->expectException(DceSecurityException::class);
        $this->expectExceptionMessage('Local domain must be a valid DCE Security domain');

        $generator->generate(42);
    }

    public function testGenerateThrowsExceptionForOrgWithoutIdentifier(): void
    {
        $numberConverter = Mockery::mock(NumberConverterInterface::class);
        $timeGenerator = Mockery::mock(TimeGeneratorInterface::class);
        $dceSecurityProvider = Mockery::mock(DceSecurityProviderInterface::class);

        $generator = new DceSecurityGenerator($numberConverter, $timeGenerator, $dceSecurityProvider);

        $this->expectException(DceSecurityException::class);
        $this->expectExceptionMessage('A local identifier must be provided for the org domain');

        $generator->generate(Uuid::DCE_DOMAIN_ORG);
    }

    public function testClockSequenceLowerBounds(): void
    {
        $dceSecurityProvider = Mockery::mock(DceSecurityProviderInterface::class);
        $nodeProvider = Mockery::mock(NodeProviderInterface::class);
        $timeProvider = new FixedTimeProvider(new Time(1583527677, 111984));

        $calculator = new BrickMathCalculator();
        $numberConverter = new GenericNumberConverter($calculator);
        $timeConverter = new GenericTimeConverter($calculator);
        $timeGenerator = new DefaultTimeGenerator($nodeProvider, $timeConverter, $timeProvider);

        $dceSecurityGenerator = new DceSecurityGenerator($numberConverter, $timeGenerator, $dceSecurityProvider);

        $bytes = $dceSecurityGenerator->generate(
            Uuid::DCE_DOMAIN_ORG,
            new IntegerObject(1001),
            new Hexadecimal('0123456789ab'),
            0
        );

        $hex = bin2hex($bytes);

        $this->assertSame('000003e9', substr($hex, 0, 8));
        $this->assertSame('5feb01ea', substr($hex, 8, 8));
        $this->assertSame('00', substr($hex, 16, 2));
        $this->assertSame('02', substr($hex, 18, 2));
        $this->assertSame('0123456789ab', substr($hex, 20));
    }

    public function testClockSequenceUpperBounds(): void
    {
        $dceSecurityProvider = Mockery::mock(DceSecurityProviderInterface::class);
        $nodeProvider = Mockery::mock(NodeProviderInterface::class);
        $timeProvider = new FixedTimeProvider(new Time(1583527677, 111984));

        $calculator = new BrickMathCalculator();
        $numberConverter = new GenericNumberConverter($calculator);
        $timeConverter = new GenericTimeConverter($calculator);
        $timeGenerator = new DefaultTimeGenerator($nodeProvider, $timeConverter, $timeProvider);

        $dceSecurityGenerator = new DceSecurityGenerator($numberConverter, $timeGenerator, $dceSecurityProvider);

        $bytes = $dceSecurityGenerator->generate(
            Uuid::DCE_DOMAIN_ORG,
            new IntegerObject(1001),
            new Hexadecimal('0123456789ab'),
            63
        );

        $hex = bin2hex($bytes);

        $this->assertSame('000003e9', substr($hex, 0, 8));
        $this->assertSame('5feb01ea', substr($hex, 8, 8));
        $this->assertSame('3f', substr($hex, 16, 2));
        $this->assertSame('02', substr($hex, 18, 2));
        $this->assertSame('0123456789ab', substr($hex, 20));
    }

    public function testExceptionThrownWhenClockSequenceTooLow(): void
    {
        $dceSecurityProvider = Mockery::mock(DceSecurityProviderInterface::class);
        $timeGenerator = Mockery::mock(TimeGeneratorInterface::class);
        $numberConverter = Mockery::mock(NumberConverterInterface::class);

        $dceSecurityGenerator = new DceSecurityGenerator($numberConverter, $timeGenerator, $dceSecurityProvider);

        $this->expectException(DceSecurityException::class);
        $this->expectExceptionMessage(
            'Clock sequence out of bounds; it must be a value between 0 and 63'
        );

        $dceSecurityGenerator->generate(Uuid::DCE_DOMAIN_ORG, null, null, -1);
    }

    public function testExceptionThrownWhenClockSequenceTooHigh(): void
    {
        $dceSecurityProvider = Mockery::mock(DceSecurityProviderInterface::class);
        $timeGenerator = Mockery::mock(TimeGeneratorInterface::class);
        $numberConverter = Mockery::mock(NumberConverterInterface::class);

        $dceSecurityGenerator = new DceSecurityGenerator($numberConverter, $timeGenerator, $dceSecurityProvider);

        $this->expectException(DceSecurityException::class);
        $this->expectExceptionMessage(
            'Clock sequence out of bounds; it must be a value between 0 and 63'
        );

        $dceSecurityGenerator->generate(Uuid::DCE_DOMAIN_ORG, null, null, 64);
    }

    public function testExceptionThrownWhenLocalIdTooLow(): void
    {
        $dceSecurityProvider = Mockery::mock(DceSecurityProviderInterface::class);
        $timeGenerator = Mockery::mock(TimeGeneratorInterface::class);
        $numberConverter = Mockery::mock(NumberConverterInterface::class);

        $dceSecurityGenerator = new DceSecurityGenerator($numberConverter, $timeGenerator, $dceSecurityProvider);

        $this->expectException(DceSecurityException::class);
        $this->expectExceptionMessage(
            'Local identifier out of bounds; it must be a value between 0 and 4294967295'
        );

        $dceSecurityGenerator->generate(Uuid::DCE_DOMAIN_ORG, new IntegerObject(-1));
    }

    public function testExceptionThrownWhenLocalIdTooHigh(): void
    {
        $dceSecurityProvider = Mockery::mock(DceSecurityProviderInterface::class);
        $timeGenerator = Mockery::mock(TimeGeneratorInterface::class);

        $calculator = new BrickMathCalculator();
        $numberConverter = new GenericNumberConverter($calculator);

        $dceSecurityGenerator = new DceSecurityGenerator($numberConverter, $timeGenerator, $dceSecurityProvider);

        $this->expectException(DceSecurityException::class);
        $this->expectExceptionMessage(
            'Local identifier out of bounds; it must be a value between 0 and 4294967295'
        );

        $dceSecurityGenerator->generate(Uuid::DCE_DOMAIN_ORG, new IntegerObject('4294967296'));
    }
}
