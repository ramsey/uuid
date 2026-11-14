<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Validator;

use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\Validator\GenericValidator;

use function array_merge;
use function str_repeat;
use function strlen;
use function strtoupper;

class GenericValidatorTest extends TestCase
{
    /**
     * @dataProvider provideValuesForValidation
     */
    public function testValidate(string $value, bool $expected): void
    {
        $variations = [];
        $variations[] = $value;
        $variations[] = 'urn:uuid:' . $value;
        $variations[] = '{' . $value . '}';

        foreach ($variations as $variation) {
            $variations[] = strtoupper($variation);
        }

        $validator = new GenericValidator();

        foreach ($variations as $variation) {
            $this->assertSame($expected, $validator->validate($variation));
        }
    }

    /**
     * @return array<array{value: string, expected: bool}>
     */
    public function provideValuesForValidation(): array
    {
        $hexMutations = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 'a', 'b', 'c', 'd', 'e', 'f'];

        $testValues = [];

        foreach ($hexMutations as $version) {
            foreach ($hexMutations as $variant) {
                $testValues[] = [
                    'value' => "ff6f8cb0-c57d-{$version}1e1-{$variant}b21-0800200c9a66",
                    'expected' => true,
                ];
            }
        }

        return array_merge($testValues, [
            [
                'value' => 'zf6f8cb0-c57d-11e1-9b21-0800200c9a66',
                'expected' => false,
            ],
            [
                'value' => '3f6f8cb0-c57d-11e1-9b21-0800200c9a6',
                'expected' => false,
            ],
            [
                'value' => 'af6f8cb-c57d-11e1-9b21-0800200c9a66',
                'expected' => false,
            ],
            [
                'value' => 'af6f8cb0c57d11e19b210800200c9a66',
                'expected' => false,
            ],
            [
                'value' => 'ff6f8cb0-c57da-51e1-9b21-0800200c9a66',
                'expected' => false,
            ],
            [
                'value' => "ff6f8cb0-c57d-11e1-1b21-0800200c9a66\n",
                'expected' => false,
            ],
            [
                'value' => "\nff6f8cb0-c57d-11e1-1b21-0800200c9a66",
                'expected' => false,
            ],
            [
                'value' => "\nff6f8cb0-c57d-11e1-1b21-0800200c9a66\n",
                'expected' => false,
            ],
        ]);
    }

    public function testGetPattern(): void
    {
        $expectedPattern = '\A[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}\z';

        $validator = new GenericValidator();

        $this->assertSame($expectedPattern, $validator->getPattern());
    }

    /**
     * @dataProvider provideRefusedWrappers
     */
    public function testValidateRefusesRepeatedOrUnmatchedWrappers(string $value): void
    {
        $this->assertFalse((new GenericValidator())->validate($value));
    }

    /**
     * @return array<string, array{string}>
     */
    public function provideRefusedWrappers(): array
    {
        return [
            'repeated prefix' => ['urn:uuid:urn:uuid:ff6f8cb0-c57d-11e1-9b21-0800200c9a66'],
            'prefixes in reverse order' => ['uuid:urn:ff6f8cb0-c57d-11e1-9b21-0800200c9a66'],
            'prefix after the UUID' => ['ff6f8cb0-c57d-11e1-9b21-0800200c9a66uuid:'],
            'prefix inside the UUID' => ['ff6f8cb0-urn:c57d-11e1-9b21-0800200c9a66'],
            'prefix inside braces' => ['{urn:uuid:ff6f8cb0-c57d-11e1-9b21-0800200c9a66}'],
            'two pairs of braces' => ['{{ff6f8cb0-c57d-11e1-9b21-0800200c9a66}}'],
            'opening brace only' => ['{ff6f8cb0-c57d-11e1-9b21-0800200c9a66'],
            'closing brace only' => ['ff6f8cb0-c57d-11e1-9b21-0800200c9a66}'],
            'extra closing braces' => ['{ff6f8cb0-c57d-11e1-9b21-0800200c9a66}}}'],
            'brace inside the UUID' => ['ff6f8cb0-{c57d}-11e1-9b21-0800200c9a66'],
            'trailing newline after braces' => ["{ff6f8cb0-c57d-11e1-9b21-0800200c9a66}\n"],
        ];
    }

    /**
     * @dataProvider provideAcceptedWrappers
     */
    public function testValidateAcceptsOnePrefixAndOnePairOfBraces(string $value): void
    {
        $this->assertTrue((new GenericValidator())->validate($value));
    }

    /**
     * @return array<string, array{string}>
     */
    public function provideAcceptedWrappers(): array
    {
        return [
            'urn:uuid: prefix' => ['urn:uuid:ff6f8cb0-c57d-11e1-9b21-0800200c9a66'],
            'uppercase prefix' => ['URN:UUID:ff6f8cb0-c57d-11e1-9b21-0800200c9a66'],
            'uuid: prefix' => ['uuid:ff6f8cb0-c57d-11e1-9b21-0800200c9a66'],
            'urn: prefix' => ['urn:ff6f8cb0-c57d-11e1-9b21-0800200c9a66'],
            'braces' => ['{ff6f8cb0-c57d-11e1-9b21-0800200c9a66}'],
            'prefix and braces' => ['urn:uuid:{ff6f8cb0-c57d-11e1-9b21-0800200c9a66}'],
        ];
    }

    public function testValidateAcceptsTheLongestForm(): void
    {
        $value = 'urn:uuid:{ff6f8cb0-c57d-11e1-9b21-0800200c9a66}';

        $this->assertSame(47, strlen($value));
        $this->assertTrue((new GenericValidator())->validate($value));
    }

    /**
     * @dataProvider provideValuesLongerThanTheLongestForm
     */
    public function testValidateRefusesValueLongerThanTheLongestForm(string $value): void
    {
        $this->assertFalse((new GenericValidator())->validate($value));
    }

    /**
     * @return array<string, array{string}>
     */
    public function provideValuesLongerThanTheLongestForm(): array
    {
        return [
            'one character longer' => ['urn:uuid:{ff6f8cb0-c57d-11e1-9b21-0800200c9a66} '],
            'a second closing brace' => ['urn:uuid:{ff6f8cb0-c57d-11e1-9b21-0800200c9a66}}'],
            'a very long value' => ['{' . str_repeat('a', 100000)],
        ];
    }
}
