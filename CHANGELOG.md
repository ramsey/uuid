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
