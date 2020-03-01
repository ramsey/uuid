<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Generator;

use Mockery;
use Ramsey\Uuid\Converter\Number\GenericNumberConverter;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Converter\Time\GenericTimeConverter;
use Ramsey\Uuid\Exception\InvalidArgumentException;
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
     * @param mixed $uid
     * @param mixed $gid
     * @param mixed $seconds
     * @param mixed $microseconds
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
        ?int $providedClockSeq,
        string $expectedId,
        string $expectedDomain,
        string $expectedNode,
        string $expectedTimeMidHi
    ): void {
        $dceSecurityProvider = Mockery::mock(DceSecurityProviderInterface::class, [
            'getUid' => new IntegerObject($uid),
            'getGid' => new IntegerObject($gid),
        ]);

        $nodeProvider = Mockery::mock(NodeProviderInterface::class, [
            'getNode' => new Hexadecimal($node),
        ]);

        $timeProvider = new FixedTimeProvider(new Time($seconds, $microseconds));

        $calculator = new BrickMathCalculator();
        $numberConverter = new GenericNumberConverter($calculator);
        $timeConverter = new GenericTimeConverter($calculator);
        $timeGenerator = new DefaultTimeGenerator($nodeProvider, $timeConverter, $timeProvider);

        $dceSecurityGenerator = new DceSecurityGenerator($numberConverter, $timeGenerator, $dceSecurityProvider);

        $bytes = $dceSecurityGenerator->generate($providedDomain, $providedId, $providedNode, $providedClockSeq);

        $this->assertSame($expectedId, bin2hex(substr($bytes, 0, 4)));
        $this->assertSame($expectedDomain, bin2hex(substr($bytes, 9, 1)));
        $this->assertSame($expectedNode, bin2hex(substr($bytes, 10)));
        $this->assertSame($expectedTimeMidHi, bin2hex(substr($bytes, 4, 4)));
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
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
                'providedClockSeq' => null,
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
                'providedClockSeq' => null,
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
                'providedClockSeq' => null,
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

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Local domain must be a valid DCE Security domain');

        $generator->generate(42);
    }

    public function testGenerateThrowsExceptionForOrgWithoutIdentifier(): void
    {
        $numberConverter = Mockery::mock(NumberConverterInterface::class);
        $timeGenerator = Mockery::mock(TimeGeneratorInterface::class);
        $dceSecurityProvider = Mockery::mock(DceSecurityProviderInterface::class);

        $generator = new DceSecurityGenerator($numberConverter, $timeGenerator, $dceSecurityProvider);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('A local identifier must be provided for the org domain');

        $generator->generate(Uuid::DCE_DOMAIN_ORG);
    }
}
