<?php

/**
 * Stubs of ext-fast_uuid for static analysis
 *
 * phpcs:ignoreFile
 */

namespace FastUuid\Exception {
    if (!class_exists(InvalidArgumentException::class, false)) {
        class InvalidArgumentException extends \InvalidArgumentException
        {
        }
    }
}

namespace FastUuid {
    if (!class_exists(Uuid::class, false)) {
        final class Uuid
        {
            /** @phpstan-pure */
            public static function uuid1(int|string|null $node = null, ?int $clockSeq = null): self {} // @phpstan-ignore-line

            /** @phpstan-pure */
            public static function fromString(string $uuid): self {} // @phpstan-ignore-line

            /** @phpstan-pure */
            public static function fromHexadecimal(string $hex, bool $pad = false): self {} // @phpstan-ignore-line

            /** @phpstan-pure */
            public static function fromInteger(string $integer): self {} // @phpstan-ignore-line

            /** @phpstan-pure */
            public function getBytes(): string {} // @phpstan-ignore-line

            /** @phpstan-pure */
            public function getHex(): string {} // @phpstan-ignore-line

            /**
             * @phpstan-pure
             * @return numeric-string
             */
            public function getInteger(): string {} // @phpstan-ignore-line

            /** @phpstan-pure */
            public function getDateTime(): \DateTimeImmutable {} // @phpstan-ignore-line
        }
    }
}

namespace {
    if (!function_exists('fast_uuid_random_bytes')) {
        function fast_uuid_random_bytes(int $length): string {} // @phpstan-ignore-line
    }
    if (!function_exists('uuid_from_bin')) {
        /**
         * @phpstan-pure
         * @return non-empty-string
         */
        function uuid_from_bin(string $bytes): string {} // @phpstan-ignore-line
    }
    if (!function_exists('uuid_is_valid')) {
        /** @phpstan-pure */
        function uuid_is_valid(string $uuid, bool $strict = false): bool {} // @phpstan-ignore-line
    }
    if (!function_exists('uuid_v7')) {
        /** @return non-empty-string */
        function uuid_v7(): string {} // @phpstan-ignore-line
    }
    if (!function_exists('uuid_v7_bin')) {
        function uuid_v7_bin(): string {} // @phpstan-ignore-line
    }
    if (!function_exists('uuid_to_bin')) {
        /**
         * @phpstan-pure
         * @return non-empty-string
         */
        function uuid_to_bin(string $uuid): string {} // @phpstan-ignore-line
    }
}
