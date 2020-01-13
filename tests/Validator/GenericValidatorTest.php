<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Validator;

use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\Validator\GenericValidator;

class GenericValidatorTest extends TestCase
{
    /**
     * @dataProvider provideValuesForValidation
     */
    public function testValidate(string $value, bool $expected): void
    {
        $validator = new GenericValidator();

        $this->assertSame($expected, $validator->validate($value));
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
     */
    public function provideValuesForValidation(): array
    {
        return [
            'good version 1' => [
                'value' => 'ff6f8cb0-c57d-11e1-9b21-0800200c9a66',
                'expected' => true,
            ],
            'good version 2' => [
                'value' => 'ff6f8cb0-c57d-21e1-9b21-0800200c9a66',
                'expected' => true,
            ],
            'good version 3' => [
                'value' => 'ff6f8cb0-c57d-31e1-9b21-0800200c9a66',
                'expected' => true,
            ],
            'good version 4' => [
                'value' => 'ff6f8cb0-c57d-41e1-9b21-0800200c9a66',
                'expected' => true,
            ],
            'good version 5' => [
                'value' => 'ff6f8cb0-c57d-51e1-9b21-0800200c9a66',
                'expected' => true,
            ],
            'good upper case' => [
                'value' => 'FF6F8CB0-C57D-11E1-9B21-0800200C9A66',
                'expected' => true,
            ],
            'bad hex' => [
                'value' => 'zf6f8cb0-c57d-11e1-9b21-0800200c9a66',
                'expected' => false,
            ],
            'too short 1' => [
                'value' => '3f6f8cb0-c57d-11e1-9b21-0800200c9a6',
                'expected' => false,
            ],
            'too short 2' => [
                'value' => 'af6f8cb-c57d-11e1-9b21-0800200c9a66',
                'expected' => false,
            ],
            'no dashes' => [
                'value' => 'af6f8cb0c57d11e19b210800200c9a66',
                'expected' => false,
            ],
            'too long' => [
                'value' => 'ff6f8cb0-c57da-51e1-9b21-0800200c9a66',
                'expected' => false,
            ],
        ];
    }
}
