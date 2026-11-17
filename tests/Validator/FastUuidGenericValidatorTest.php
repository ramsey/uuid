<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Validator;

use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\Validator\GenericValidator;

use function preg_match;
use function preg_replace;
use function str_repeat;
use function strlen;
use function strtoupper;
use function substr_replace;

/**
 * @requires extension fast_uuid
 */
class FastUuidGenericValidatorTest extends TestCase
{
    /**
     * @dataProvider provideStrings
     */
    public function testValidateMatchesThePatternWithTheExtensionLoaded(string $value): void
    {
        $this->assertSame($this->expected($value), (new GenericValidator())->validate($value));
    }

    /**
     * @return array<string, array{string}>
     */
    public function provideStrings(): array
    {
        $uuid = 'ff6f8cb0-c57d-11e1-9b21-0800200c9a66';
        $hex = 'ff6f8cb0c57d11e19b210800200c9a66';
        $values = [
            'canonical' => [$uuid],
            'upper case' => [strtoupper($uuid)],
            'nil' => ['00000000-0000-0000-0000-000000000000'],
            'max' => ['ffffffff-ffff-ffff-ffff-ffffffffffff'],
            'urn: before 32 digits' => ['urn:' . $hex],
            'URN: before 32 digits' => ['URN:' . $hex],
            'two pairs of braces around 32 digits' => ['{{' . $hex . '}}'],
            'braces around 34 characters' => ['{' . substr($uuid, 0, 34) . '}'],
            'urn:uuid: and braces' => ['urn:uuid:{' . $uuid . '}'],
            'braces around urn:uuid:' => ['{urn:uuid:' . $uuid . '}'],
            '32 digits' => [$hex],
            '36 hyphens' => [str_repeat('-', 36)],
            'newline after 35 characters' => [substr($uuid, 0, 35) . "\n"],
        ];

        foreach (['g', 'G', '-', ' ', "\n", "\0", '{', '}', ':', "\xff"] as $char) {
            for ($offset = 0; $offset < 36; $offset++) {
                $values['"' . $char . '" at offset ' . $offset] = [substr_replace($uuid, $char, $offset, 1)];
            }
        }

        $prefixes = ['', 'urn:', 'uuid:', 'urn:uuid:', 'URN:UUID:', 'uuid:urn:', 'urn:urn:', 'Uuid:'];
        $inners = [
            $uuid,
            $hex,
            substr($uuid, 1),
            $uuid . 'a',
            substr_replace($uuid, 'g', 35, 1),
            substr_replace($uuid, '-', 0, 1),
        ];
        foreach ($inners as $i => $inner) {
            foreach ($prefixes as $outer) {
                foreach ($prefixes as $inside) {
                    foreach (
                        [
                            $outer . $inside . $inner,
                            $outer . '{' . $inside . $inner . '}',
                            '{' . $outer . '{' . $inside . $inner . '}}',
                            $outer . '{{' . $inside . $inner . '}}',
                            $outer . $inside . '{' . $inner,
                            $outer . $inside . $inner . '}',
                        ] as $form
                    ) {
                        $values['inner ' . $i . ': ' . $form] = [$form];
                    }
                }
            }
        }

        return $values;
    }

    /**
     * The rules of GenericValidator without the extension
     */
    private function expected(string $value): bool
    {
        if (strlen($value) > 47) {
            return false;
        }

        $value = preg_replace('/\A(?:urn:)?(?:uuid:)?(?:\{(.*)\}|(.*))\z/Dis', '$1$2', $value) ?? $value;

        return preg_match('/' . (new GenericValidator())->getPattern() . '/Dms', $value) === 1;
    }
}
