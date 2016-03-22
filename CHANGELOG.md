# Rhumsaa\Uuid Changelog

## 2.9.0

_Released: 2016-03-22_

  * Drop support for OpenSSL in favor of [paragonie/random_compat][]. This addresses and fixes the [collision issue][].

## 2.8.4

_Released: 2015-12-17_

  * Add support for symfony/console v3.
  * Update build matrix to run Travis CI tests on PHP 7 & with lowest package versions.

## 2.8.3

_Released: 2015-08-31_

  * Fix exception message in `Uuid::calculateUuidTime()`.
  * Update composer.json to reflect new repository and package name.

## 2.8.2

_Released: 2015-07-23_

  * Ensure the release tag makes it into the rhumsaa/uuid package.
  * Minor documentation changes.

## 2.8.1

_Released: 2015-06-16_

  * Use `passthru()` and output buffering in `getIfconfig()`.
  * Cache the system node in a static variable so that we process it only once per runtime.
  * Set ramsey/uuid as a replacement for rhumsaa/uuid in composer.json.
  * Documentation updates and corrections.

## 2.8.0

_Released: 2014-11-09_

  * Added static `fromInteger()` method to create UUIDs from string integer or `\Moontoast\Math\BigNumber`.
  * Friendlier Doctrine conversion to Uuid or string.
  * Documentation fixes.

## 2.7.4

_Released: 2014-10-29_

  * Changed loop in `generateBytes()` from `foreach` to `for`; see #33
  * Use `toString()` in README examples to avoid confusion
  * Exclude build/development tools from releases using .gitattributes
  * Set timezone properly for tests

## 2.7.3

_Released: 2014-08-27_

  * Fixed upper range for `mt_rand` used in version 4 UUIDs

## 2.7.2

_Released: 2014-07-28_

  * Upgraded to PSR-4 autoloading
  * Testing upgrades:
    * Testing against PHP 5.6
    * Testing with PHPUnit 4
    * Using Coveralls.io to generate code coverage reports
  * Documentation fixes

## 2.7.1

_Released: 2014-02-19_

  * Moved moontoast/math and symfony/console to require-dev; fixes #20
  * Now supporting symfony/console for 2.3 (LTS version); fixes #21
  * Updated tests to run even when dev packages are not installed (skips tests if requirements are not met)

## 2.7.0

_Released: 2014-01-31_

  * Moved UUID validation regex pattern into constant for external use (`Uuid::VALID_PATTERN`)

## 2.6.1

_Released: 2014-01-27_

  * Fixed bug where `uuid` console application could not find the Composer autoloader when installed in another project

## 2.6.0

_Released: 2014-01-17_

  * Introduced `uuid` console application for generating and decoding UUIDs from CLI (run `./bin/uuid` for details)
  * Added `Uuid::getInteger()` to retrieve a Moontoast\Math\BigNumber representation of the 128-bit integer representing the UUID
  * Added `Uuid::getHex()` to retrieve the hexadecimal representation of the UUID
  * Now using netstat on Linux to capture the node for a version 1 UUID
  * Now requiring Moontoast\Math as part of the regular package requirements, not just the dev requirements

## 2.5.0

_Released: 2013-10-30_

  * Using `openssl_random_pseudo_bytes()`, if available, to generate random bytes, by merging in PR #15 from @dfreudenberger
  * Fixed test for Rhumsaa\Uuid\Doctrine\UuidType, by merging in PR #17 from @dfreudenberger
  * Documentation fixes

## 2.4.0

_Released: 2013-07-29_

  * `Uuid::getVersion()` now returns null if the UUID isn't an RFC 4122 variant
  * `Uuid::fromString()` now supports a 128-bit integer formatted as a hexadecimal string (UUID without dashes)
  * Tests have been greatly enhanced, borrowing from the Python UUID library

## 2.3.0

_Released: 2013-07-16_

  * Added `Uuid::fromBytes()` by merging in PR #14 from @asm89

## 2.2.0

_Released: 2013-07-04_

  * Added `Doctrine\UuidType::requiresSQLCommentHint()` method by merging in PR #13 from @zerrvox
  * Removed `"minimum-stability": "dev"` from composer.json

## 2.1.2

_Released: 2013-07-03_

  * @ericthelin found cases where the system node was coming back with uppercase hexadecimal digits; this ensures that case in the node is converted to lowercase

## 2.1.1

_Released: 2013-04-29_

  * Fixed NIL bug in `Uuid::isValid()` method, reported by @ocubom in PR #11

## 2.1.0

_Released: 2013-04-15_

  * Added static `Uuid::isValid()` method for checking whether a string is a valid UUID

## 2.0.0

_Released: 2013-02-11_

  * Break: `Uuid` class is now marked as "final"
  * Break: `Uuid::getLeastSignificantBits()` no longer returns an integer on 64-bit platforms; it requires `moontoast/math`
  * Break: `Uuid::getMostSignificantBits()` no longer returns an integer on 64-bit platforms; it requires `moontoast/math`
  * Break: Moved `UnsupportedOperationException` to the `Exception` subnamespace
  * Added support for 32-bit platforms
  * Added generated API documentation to the repository

## 1.1.2

_Released: 2012-11-29_

  * Relaxed Doctrine type conversion rules

## 1.1.1

_Released: 2012-08-27_

  * Removed `final` keyword from `Uuid` class

## 1.1.0

_Released: 2012-08-06_

  * Added `Doctrine\UuidType` as a field mapping type for the Doctrine Database Abstraction Layer (DBAL)
  * Improved tests and code coverage

## 1.0.0

_Released: 2012-07-19_

  * Initial release


[paragonie/random_compat]: https://github.com/paragonie/random_compat
[collision issue]: https://github.com/ramsey/uuid/issues/80
