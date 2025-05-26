<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Validator;

use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\Validator\GenericValidator;

use function array_merge;
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
}
