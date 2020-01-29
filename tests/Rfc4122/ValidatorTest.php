<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Rfc4122;

use Ramsey\Uuid\Rfc4122\Validator;
use Ramsey\Uuid\Test\TestCase;

use function array_merge;
use function in_array;
use function strtoupper;

class ValidatorTest extends TestCase
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

        $validator = new Validator();

        foreach ($variations as $variation) {
            $this->assertSame($expected, $validator->validate($variation));
        }
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
     */
    public function provideValuesForValidation(): array
    {
        $hexMutations = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 'a', 'b', 'c', 'd', 'e', 'f'];
        $trueVersions = [1, 2, 3, 4, 5];
        $trueVariants = [8, 9, 'a', 'b'];

        $testValues = [];

        foreach ($hexMutations as $version) {
            foreach ($hexMutations as $variant) {
                $testValues[] = [
                    'value' => "ff6f8cb0-c57d-{$version}1e1-{$variant}b21-0800200c9a66",
                    'expected' => in_array($variant, $trueVariants, true) && in_array($version, $trueVersions, true),
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
        ]);
    }
}
