<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test;

use Mockery;
use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Converter\Time\GenericTimeConverter;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Exception\DateTimeException;
use Ramsey\Uuid\Math\BrickMathCalculator;
use Ramsey\Uuid\Rfc4122\Fields;
use Ramsey\Uuid\Rfc4122\FieldsInterface;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Type\Time;
use Ramsey\Uuid\Uuid;

class DeprecatedUuidMethodsTraitTest extends TestCase
{
    public function testGetDateTime(): void
    {
        $calculator = new BrickMathCalculator();

        $fields = new Fields((string) hex2bin('ff6f8cb0c57d11e19b210800200c9a66'));
        $numberConverter = Mockery::mock(NumberConverterInterface::class);
        $codec = Mockery::mock(CodecInterface::class);
        $timeConverter = new GenericTimeConverter($calculator);

        $uuid = new Uuid($fields, $numberConverter, $codec, $timeConverter);

        $this->assertSame('2012-07-04T02:14:34+00:00', $uuid->getDateTime()->format('c'));
        $this->assertSame('1341368074.491000', $uuid->getDateTime()->format('U.u'));
    }

    public function testGetDateTimeThrowsException(): void
    {
        $fields = Mockery::mock(FieldsInterface::class, [
            'getVersion' => 1,
            'getTimestamp' => new Hexadecimal('0'),
        ]);

        $numberConverter = Mockery::mock(NumberConverterInterface::class);
        $codec = Mockery::mock(CodecInterface::class);

        $timeConverter = Mockery::mock(TimeConverterInterface::class, [
            'convertTime' => new Time('0', '1234567'),
        ]);

        $uuid = new Uuid($fields, $numberConverter, $codec, $timeConverter);

        $this->expectException(DateTimeException::class);

        $uuid->getDateTime();
    }
}
