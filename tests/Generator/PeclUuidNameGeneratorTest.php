<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Generator;

use Ramsey\Uuid\BinaryUtils;
use Ramsey\Uuid\Exception\NameException;
use Ramsey\Uuid\Generator\PeclUuidNameGenerator;
use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\Uuid;

use function hash;
use function pack;
use function substr;
use function substr_replace;
use function unpack;

class PeclUuidNameGeneratorTest extends TestCase
{
    /**
     * @param non-empty-string $ns
     *
     * @dataProvider provideNamesForHashingTest
     * @requires extension uuid
     */
    public function testPeclUuidNameGeneratorHashesName(string $ns, string $name, string $algorithm): void
    {
        $namespace = Uuid::fromString($ns);
        $version = $algorithm === 'md5' ? 3 : 5;
        $expectedBytes = substr(hash($algorithm, $namespace->getBytes() . $name, true), 0, 16);

        // Need to add the version and variant, since ext-uuid already includes
        // these in the values returned.
        /** @var int[] $unpackedTime */
        $unpackedTime = unpack('n*', substr($expectedBytes, 6, 2));
        $timeHi = $unpackedTime[1];
        $timeHiAndVersion = pack('n*', BinaryUtils::applyVersion($timeHi, $version));

        /** @var int[] $unpackedClockSeq */
        $unpackedClockSeq = unpack('n*', substr($expectedBytes, 8, 2));
        $clockSeqHi = $unpackedClockSeq[1];
        $clockSeqHiAndReserved = pack('n*', BinaryUtils::applyVariant($clockSeqHi));

        $expectedBytes = substr_replace($expectedBytes, $timeHiAndVersion, 6, 2);
        $expectedBytes = substr_replace($expectedBytes, $clockSeqHiAndReserved, 8, 2);

        $generator = new PeclUuidNameGenerator();
        $generatedBytes = $generator->generate($namespace, $name, $algorithm);

        $this->assertSame(
            $expectedBytes,
            $generatedBytes,
            'Expected: ' . bin2hex($expectedBytes) . '; Received: ' . bin2hex($generatedBytes)
        );
    }

    /**
     * @return array<array{ns: string, name: string, algorithm: string}>
     */
    public function provideNamesForHashingTest(): array
    {
        return [
            [
                'ns' => Uuid::NAMESPACE_URL,
                'name' => 'https://example.com/foobar',
                'algorithm' => 'md5',
            ],
            [
                'ns' => Uuid::NAMESPACE_URL,
                'name' => 'https://example.com/foobar',
                'algorithm' => 'sha1',
            ],
            [
                'ns' => Uuid::NAMESPACE_OID,
                'name' => '1.3.6.1.4.1.343',
                'algorithm' => 'sha1',
            ],
            [
                'ns' => Uuid::NAMESPACE_OID,
                'name' => '1.3.6.1.4.1.52627',
                'algorithm' => 'md5',
            ],
            [
                'ns' => 'd988ae29-674e-48e7-b93c-2825e2a96fbe',
                'name' => 'foobar',
                'algorithm' => 'sha1',
            ],
        ];
    }

    public function testGenerateThrowsException(): void
    {
        $namespace = Uuid::fromString('cd998804-c661-4264-822c-00cada75a87b');
        $generator = new PeclUuidNameGenerator();

        $this->expectException(NameException::class);
        $this->expectExceptionMessage(
            'Unable to hash namespace and name with algorithm \'aBadAlgorithm\''
        );

        $generator->generate($namespace, 'a test name', 'aBadAlgorithm');
    }
}
