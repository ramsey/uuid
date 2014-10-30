* 2.7.4 (2014-10-29)
  * Changed loop in `generateBytes()` from `foreach` to `for`; see #33
  * Use `toString()` in README examples to avoid confusion
  * Exclude build/development tools from releases using .gitattributes
  * Set timezone properly for tests
* 2.7.3 (2014-08-27)
  * Fixed upper range for `mt_rand` used in version 4 UUIDs
* 2.7.2 (2014-07-28)
  * Upgraded to PSR-4 autoloading
  * Testing upgrades:
    * Testing against PHP 5.6
    * Testing with PHPUnit 4
    * Using Coveralls.io to generate code coverage reports
  * Documentation fixes
* 2.7.1 (2014-02-19)
  * Moved moontoast/math and symfony/console to require-dev; fixes #20
  * Now supporting symfony/console for 2.3 (LTS version); fixes #21
  * Updated tests to run even when dev packages are not installed (skips tests if requirements are not met)
* 2.7.0 (2014-01-31)
  * Moved UUID validation regex pattern into constant for external use (`Uuid::VALID_PATTERN`)
* 2.6.1 (2014-01-27)
  * Fixed bug where `uuid` console application could not find the Composer autoloader when installed in another project
* 2.6.0 (2014-01-17)
  * Introduced `uuid` console application for generating and decoding UUIDs from CLI (run `./bin/uuid` for details)
  * Added `Uuid::getInteger()` to retrieve a Moontoast\Math\BigNumber representation of the 128-bit integer representing the UUID
  * Added `Uuid::getHex()` to retrieve the hexadecimal representation of the UUID
  * Now using netstat on Linux to capture the node for a version 1 UUID
  * Now requiring Moontoast\Math as part of the regular package requirements, not just the dev requirements
* 2.5.0 (2013-10-30)
  * Using `openssl_random_pseudo_bytes()`, if available, to generate random bytes, by merging in PR #15 from @dfreudenberger
  * Fixed test for Rhumsaa\Uuid\Doctrine\UuidType, by merging in PR #17 from @dfreudenberger
  * Documentation fixes
* 2.4.0 (2013-07-29)
  * `Uuid::getVersion()` now returns null if the UUID isn't an RFC 4122 variant
  * `Uuid::fromString()` now supports a 128-bit integer formatted as a hexadecimal string (UUID without dashes)
  * Tests have been greatly enhanced, borrowing from the Python UUID library
* 2.3.0 (2013-07-16)
  * Added `Uuid::fromBytes()` by merging in PR #14 from @asm89
* 2.2.0 (2013-07-04)
  * Added `Doctrine\UuidType::requiresSQLCommentHint()` method by merging in PR #13 from @zerrvox
  * Removed `"minimum-stability": "dev"` from composer.json
* 2.1.2 (2013-07-03)
  * @ericthelin found cases where the system node was coming back with uppercase hexadecimal digits; this ensures that case in the node is converted to lowercase
* 2.1.1 (2013-04-29)
  * Fixed NIL bug in `Uuid::isValid()` method, reported by @ocubom in PR #11
* 2.1.0 (2013-04-15)
  * Added static `Uuid::isValid()` method for checking whether a string is a valid UUID
* 2.0.0 (2013-02-11)
  * Break: `Uuid` class is now marked as "final"
  * Break: `Uuid::getLeastSignificantBits()` no longer returns an integer on 64-bit platforms; it requires `moontoast/math`
  * Break: `Uuid::getMostSignificantBits()` no longer returns an integer on 64-bit platforms; it requires `moontoast/math`
  * Break: Moved `UnsupportedOperationException` to the `Exception` subnamespace
  * Added support for 32-bit platforms
  * Added generated API documentation to the repository
* 1.1.2 (2012-11-29)
  * Relaxed Doctrine type conversion rules
* 1.1.1 (2012-08-27)
  * Removed `final` keyword from `Uuid` class
* 1.1.0 (2012-08-06)
  * Added `Doctrine\UuidType` as a field mapping type for the Doctrine Database Abstraction Layer (DBAL)
  * Improved tests and code coverage
* 1.0.0 (2012-07-19)
  * Initial release
