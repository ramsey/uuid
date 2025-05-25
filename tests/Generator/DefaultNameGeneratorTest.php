<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Generator;

use Ramsey\Uuid\Exception\NameException;
use Ramsey\Uuid\Generator\DefaultNameGenerator;
use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\Uuid;

use function hash;

class DefaultNameGeneratorTest extends TestCase
{
    /**
     * @param non-empty-string $ns
     * @param non-empty-string $name
     * @param non-empty-string $algorithm
     *
     * @dataProvider provideNamesForHashingTest
     */
    public function testDefaultNameGeneratorHashesName(string $ns, string $name, string $algorithm): void
    {
        $namespace = Uuid::fromString($ns);
        $expectedBytes = hash($algorithm, $namespace->getBytes() . $name, true);

        $generator = new DefaultNameGenerator();

        $this->assertSame($expectedBytes, $generator->generate($namespace, $name, $algorithm));
    }

    /**
     * @return array<array{ns: non-empty-string, name: non-empty-string, algorithm: non-empty-string}>
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
                'ns' => Uuid::NAMESPACE_URL,
                'name' => 'https://example.com/foobar',
                'algorithm' => 'sha256',
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
        $generator = new DefaultNameGenerator();

        $this->expectException(NameException::class);
        $this->expectExceptionMessage(
            'Unable to hash namespace and name with algorithm \'aBadAlgorithm\''
        );

        $generator->generate($namespace, 'a test name', 'aBadAlgorithm');
    }
}
