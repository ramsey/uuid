<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Guid;

use Mockery;
use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Guid\DegradedGuid;
use Ramsey\Uuid\Test\TestCase;

class DegradedGuidTest extends TestCase
{
    public function testConstructorConstructsDegradedGuid(): void
    {
        $numberConverter = Mockery::mock(NumberConverterInterface::class);
        $codec = Mockery::mock(CodecInterface::class);
        $timeConverter = Mockery::mock(TimeConverterInterface::class);

        $fields = [
            'b1484596',
            '25dc',
            '11ea',
            '978f',
            '2e728ce88125',
        ];

        $expectedFields = $fields;

        // Bytes are expected to be swapped before being passed to DegradedGuid.
        $bytes = (string) hex2bin(implode('', $fields));
        $fields[0] = bin2hex($bytes[3] . $bytes[2] . $bytes[1] . $bytes[0]);
        $fields[1] = bin2hex($bytes[5] . $bytes[4]);
        $fields[2] = bin2hex($bytes[7] . $bytes[6]);

        $degradedGuid = new DegradedGuid($fields, $numberConverter, $codec, $timeConverter);

        $this->assertInstanceOf(DegradedGuid::class, $degradedGuid);
        $this->assertSame($expectedFields[0], $degradedGuid->getTimeLowHex());
        $this->assertSame($expectedFields[1], $degradedGuid->getTimeMidHex());
        $this->assertSame($expectedFields[2], $degradedGuid->getTimeHiAndVersionHex());
        $this->assertSame(
            $expectedFields[3],
            $degradedGuid->getClockSeqHiAndReservedHex() . $degradedGuid->getClockSeqLowHex()
        );
        $this->assertSame($expectedFields[4], $degradedGuid->getNodeHex());
    }
}
