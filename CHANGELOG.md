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
