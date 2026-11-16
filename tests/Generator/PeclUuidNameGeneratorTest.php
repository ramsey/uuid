<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Generator;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use Ramsey\Uuid\Exception\NameException;
use Ramsey\Uuid\Generator\PeclUuidNameGenerator;
use Ramsey\Uuid\Rfc4122\Version;
use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\Uuid;

use function chr;
use function hash;
use function ord;
use function substr;

class PeclUuidNameGeneratorTest extends TestCase
{
    /**
     * @param non-empty-string $ns
     * @param non-empty-string $algorithm
     */
    #[DataProvider('provideNamesForHashingTest')]
    #[RequiresPhpExtension('uuid')]
    public function testPeclUuidNameGeneratorHashesName(string $ns, string $name, string $algorithm): void
    {
        $namespace = Uuid::fromString($ns);
        $version = $algorithm === 'md5' ? Version::HashMd5 : Version::HashSha1;
        $expectedBytes = substr(hash($algorithm, $namespace->getBytes() . $name, true), 0, 16);

        // Need to add the version and variant, since ext-uuid already includes
        // these in the values returned. The version is the first four bits of
        // the seventh byte, and the variant is the first two bits of the ninth.
        $expectedBytes[6] = chr(ord($expectedBytes[6]) & 0x0f | $version->value << 4);
        $expectedBytes[8] = chr(ord($expectedBytes[8]) & 0x3f | 0x80);

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
    public static function provideNamesForHashingTest(): array
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
