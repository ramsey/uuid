<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Rfc4122;

use Mockery;
use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Rfc4122\FieldsInterface;
use Ramsey\Uuid\Rfc4122\UuidV2;
use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\Type\Integer;
use Ramsey\Uuid\Uuid;

class UuidV2Test extends TestCase
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
            'Fields used to create a UuidV2 must represent a '
            . 'version 2 (DCE Security) UUID'
        );

        new UuidV2($fields, $numberConverter, $codec, $timeConverter);
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
     */
    public function provideTestVersions(): array
    {
        return [
            ['version' => 0],
            ['version' => 1],
            ['version' => 3],
            ['version' => 4],
            ['version' => 5],
            ['version' => 6],
            ['version' => 7],
            ['version' => 8],
            ['version' => 9],
        ];
    }

    /**
     * @dataProvider provideLocalDomainAndIdentifierForTests
     */
    public function testGetLocalDomainAndIdentifier(
        int $domain,
        Integer $identifier,
        int $expectedDomain,
        string $expectedDomainName,
        string $expectedIdentifier
    ): void {
        /** @var UuidV2 $uuid */
        $uuid = Uuid::uuid2($domain, $identifier);

        $this->assertSame($expectedDomain, $uuid->getLocalDomain());
        $this->assertSame($expectedDomainName, $uuid->getLocalDomainName());
        $this->assertInstanceOf(Integer::class, $uuid->getLocalIdentifier());
        $this->assertSame($expectedIdentifier, $uuid->getLocalIdentifier()->toString());
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
     */
    public function provideLocalDomainAndIdentifierForTests(): array
    {
        return [
            [
                'domain' => Uuid::DCE_DOMAIN_PERSON,
                'identifier' => new Integer('12345678'),
                'expectedDomain' => 0,
                'expectedDomainName' => 'person',
                'expectedIdentifier' => '12345678',
            ],
            [
                'domain' => Uuid::DCE_DOMAIN_GROUP,
                'identifier' => new Integer('87654321'),
                'expectedDomain' => 1,
                'expectedDomainName' => 'group',
                'expectedIdentifier' => '87654321',
            ],
            [
                'domain' => Uuid::DCE_DOMAIN_ORG,
                'identifier' => new Integer('1'),
                'expectedDomain' => 2,
                'expectedDomainName' => 'org',
                'expectedIdentifier' => '1',
            ],
            [
                'domain' => Uuid::DCE_DOMAIN_PERSON,
                'identifier' => new Integer('0'),
                'expectedDomain' => 0,
                'expectedDomainName' => 'person',
                'expectedIdentifier' => '0',
            ],
            [
                'domain' => Uuid::DCE_DOMAIN_PERSON,
                'identifier' => new Integer('4294967295'),
                'expectedDomain' => 0,
                'expectedDomainName' => 'person',
                'expectedIdentifier' => '4294967295',
            ],
        ];
    }
}
