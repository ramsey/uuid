<?php

declare(strict_types=1);

namespace Ramsey\Uuid\StaticAnalysis;

use InvalidArgumentException;
use Ramsey\Uuid\Uuid;

final class ValidUuidIsNonEmpty
{
    /** @return non-empty-string */
    public function validUuidsAreNotEmpty(string $input): string
    {
        if (Uuid::isValid($input)) {
            return $input;
        }

        throw new InvalidArgumentException('Not a UUID');
    }

    /**
     * @param non-empty-string $input
     *
     * @return non-empty-string
     */
    public function givenNonEmptyInputAssertionRemainsValid(string $input): string
    {
        if (Uuid::isValid($input)) {
            return $input;
        }

        throw new InvalidArgumentException('Not a UUID');
    }

    public function givenInvalidInputValueRemainsAString(string $input): string
    {
        if (Uuid::isValid($input)) {
            return 'It Worked!';
        }

        return $input;
    }
}
