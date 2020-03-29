# ramsey/uuid Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).


## [Unreleased]

### Added

### Changed

### Deprecated

### Removed

### Fixed

### Security


## [4.0.1] - 2020-03-29

### Fixed

* Fix collection deserialization errors due to upstream `allowed_classes` being
  set to `false`. For details, see [ramsey/uuid#303](https://github.com/ramsey/uuid/issues/303)
  and [ramsey/collection#47](https://github.com/ramsey/collection/issues/47).


## [4.0.0] - 2020-03-22

### Added

* Add support for version 6 UUIDs, as defined by <http://gh.peabody.io/uuidv6/>,
  including the static method `Uuid::uuid6()`, which returns a
  `Nonstandard\UuidV6` instance.
* Add ability to generate version 2 (DCE Security) UUIDs, including the static
  method `Uuid::uuid2()`, which returns an `Rfc4122\UuidV2` instance.
* Add classes to represent each version of RFC 4122 UUID. When generating new
  UUIDs or creating UUIDs from existing strings, bytes, or integers, if the UUID
  is an RFC 4122 variant, one of these instances will be returned:
  * `Rfc4122\UuidV1`
  * `Rfc4122\UuidV2`
  * `Rfc4122\UuidV3`
  * `Rfc4122\UuidV4`
  * `Rfc4122\UuidV5`
  * `Rfc4122\NilUuid`
* Add classes to represent version 6 UUIDs, GUIDs, and nonstandard
  (non-RFC 4122 variant) UUIDs:
  * `Nonstandard\UuidV6`
  * `Guid\Guid`
  * `Nonstandard\Uuid`
* Add `Uuid::fromDateTime()` to create version 1 UUIDs from instances of
  `\DateTimeInterface`.
* The `\DateTimeInterface` instance returned by `UuidInterface::getDateTime()`
  (and now `Rfc4122\UuidV1::getDateTime()`) now includes microseconds, as
  specified by the version 1 UUID.
* Add `Validator\ValidatorInterface` and `Validator\GenericValidator` to allow
  flexibility in validating UUIDs/GUIDs.
  * The default validator continues to validate UUID strings using the same
    relaxed validation pattern found in the 3.x series of ramsey/uuid.
  * Introduce `Rfc4122\Validator` that may be used for strict validation of
    RFC 4122 UUID strings.
  * Add ability to change the default validator used by `Uuid` through
    `FeatureSet::setValidator()`.
  * Add `getValidator()` and `setValidator()` to `UuidFactory`.
* Add `Provider\Node\StaticNodeProvider` to assist in setting a custom static
  node value with the multicast bit set for version 1 UUIDs.
* Add the following new exceptions:
  * `Exception\BuilderNotFoundException` -
    Thrown to indicate that no suitable UUID builder could be found.
  * `Exception\DateTimeException` -
    Thrown to indicate that the PHP DateTime extension encountered an
    exception/error.
  * `Exception\DceSecurityException` -
    Thrown to indicate an exception occurred while dealing with DCE Security
    (version 2) UUIDs.
  * `Exception\InvalidArgumentException` -
    Thrown to indicate that the argument received is not valid. This extends the
    built-in PHP `\InvalidArgumentException`, so there should be no BC breaks
    with ramsey/uuid throwing this exception, if you are catching the PHP
    exception.
  * `Exception\InvalidBytesException` -
    Thrown to indicate that the bytes being operated on are invalid in some way.
  * `Exception\NameException` -
    Thrown to indicate that an error occurred while attempting to hash a
    namespace and name.
  * `Exception\NodeException` -
    Throw to indicate that attempting to fetch or create a node ID encountered
    an error.
  * `Exception\RandomSourceException` -
    Thrown to indicate that the source of random data encountered an error.
  * `Exception\TimeSourceException` -
    Thrown to indicate that the source of time encountered an error.
  * `Exception\UnableToBuildUuidException` -
    Thrown to indicate a builder is unable to build a UUID.
* Introduce a `Builder\FallbackBuilder`, used by `FeatureSet` to help decide
  whether to return a `Uuid` or `Nonstandard\Uuid` when decoding a
  UUID string or bytes.
* Add `Rfc4122\UuidInterface` to specifically represent RFC 4122 variant UUIDs.
* Add `Rfc4122\UuidBuilder` to build RFC 4122 variant UUIDs. This replaces the
  existing `Builder\DefaultUuidBuilder`, which is now deprecated.
* Introduce `Math\CalculatorInterface` for representing calculators to perform
  arithmetic operations on integers.
* Depend on [brick/math](https://github.com/brick/math) for the
  `Math\BrickMathCalculator`, which is the default calculator used by this
  library when math cannot be performed in native PHP due to integer size
  limitations. The calculator is configurable and may be changed, if desired.
* Add `Converter\Number\GenericNumberConverter` and
  `Converter\Time\GenericTimeConverter` which will use the calculator provided
  to convert numbers and time to values for UUIDs.
* Introduce `Type\Hexadecimal`, `Type\Integer`, `Type\Decimal`, and `Type\Time`
  for improved type-safety when dealing with arbitrary string values.
* Add a `Type\TypeInterface` that each of the ramsey/uuid types implements.
* Add `Fields\FieldsInterface` and `Rfc4122\FieldsInterface` to define
  field layouts for UUID variants. The implementations `Rfc4122\Fields`,
  `Guid\Fields`, and `Nonstandard\Fields` store the 16-byte,
  binary string representation of the UUID internally, and these manage
  conversion of the binary string into the hexadecimal field values.
* Introduce `Builder\BuilderCollection` and `Provider\Node\NodeProviderCollection`.
  These are typed collections for providing builders and node providers to
  `Builder\FallbackBuilder` and `Provider\Node\FallbackNodeProvider`, respectively.
* Add `Generator\NameGeneratorInterface` to support alternate methods of
  generating bytes for version 3 and version 5 name-based UUID. By default,
  ramsey/uuid uses the `Generator\DefaultNameGenerator`, which uses the standard
  algorithm this library has used since the beginning. You may choose to use the
  new `Generator\PeclUuidNameGenerator` to make use of the new
  `uuid_generate_md5()` and `uuid_generate_sha1()` functions in
  [ext-uuid version 1.1.0](https://pecl.php.net/package/uuid).

### Changed

* Set minimum required PHP version to 7.2.
* This library now works on 32-bit and 64-bit systems, with no degradation in
  functionality.
* By default, the following static methods will now return specific instance
  types. This should not cause any BC breaks if typehints target `UuidInterface`:
  * `Uuid::uuid1` returns `Rfc4122\UuidV1`
  * `Uuid::uuid3` returns `Rfc4122\UuidV3`
  * `Uuid::uuid4` returns `Rfc4122\UuidV4`
  * `Uuid::uuid5` returns `Rfc4122\UuidV5`
* Accept `Type\Hexadecimal` for the `$node` parameter for
  `UuidFactoryInterface::uuid1()`. This is in addition to the `int|string` types
  already accepted, so there are no BC breaks. `Type\Hexadecimal` is now the
  recommended type to pass for `$node`.
* Out of the box, `Uuid::fromString()`, `Uuid::fromBytes()`, and
  `Uuid::fromInteger()` will now return either an `Rfc4122\UuidInterface`
  instance or an instance of `Nonstandard\Uuid`, depending on whether the input
  contains an RFC 4122 variant UUID with a valid version identifier. Both
  implement `UuidInterface`, so BC breaks should not occur if typehints use the
  interface.
* Change `Uuid::getFields()` to return an instance of `Fields\FieldsInterface`.
  Previously, it returned an array of integer values (on 64-bit systems only).
* `Uuid::getDateTime()` now returns an instance of `\DateTimeImmutable` instead
  of `\DateTime`.
* Make the following changes to `UuidInterface`:
  * `getHex()` now returns a `Type\Hexadecimal` instance.
  * `getInteger()` now returns a `Type\Integer` instance. The `Type\Integer`
    instance holds a string representation of a 128-bit integer. You may then
    use a math library of your choice (bcmath, gmp, etc.) to operate on the
    string integer.
  * `getDateTime()` now returns `\DateTimeInterface` instead of `\DateTime`.
  * Add `__toString()` method.
  * Add `getFields()` method. It returns an instance of `Fields\FieldsInterface`.
* Add the following new methods to `UuidFactoryInterface`:
  * `uuid2()`
  * `uuid6()`
  * `fromDateTime()`
  * `fromInteger()`
  * `getValidator()`
* This library no longer throws generic exceptions. However, this should not
  result in BC breaks, since the new exceptions extend from built-in PHP
  exceptions that this library previously threw.
  * `Exception\UnsupportedOperationException` is now descended from
    `\LogicException`. Previously, it descended from `\RuntimeException`.
* Change required constructor parameters for `Uuid`:
  * Change the first required constructor parameter for `Uuid` from
    `array $fields` to `Rfc4122\FieldsInterface $fields`.
  * Add `Converter\TimeConverterInterface $timeConverter` as the fourth
    required constructor parameter for `Uuid`.
* Change the second required parameter of `Builder\UuidBuilderInterface::build()`
  from `array $fields` to `string $bytes`. Rather than accepting an array of
  hexadecimal strings as UUID fields, the `build()` method now expects a byte
  string.
* Add `Converter\TimeConverterInterface $timeConverter` as the second required
  constructor parameter for `Rfc4122\UuidBuilder`. This also affects the
  now-deprecated `Builder\DefaultUuidBuilder`, since this class now inherits
  from `Rfc4122\UuidBuilder`.
* Add `convertTime()` method to `Converter\TimeConverterInterface`.
* Add `getTime()` method to `Provider\TimeProviderInterface`. It replaces the
  `currentTime()` method.
* `Provider\Node\FallbackNodeProvider` now accepts only a
  `Provider\Node\NodeProviderCollection` as its constructor parameter.
* `Provider\Time\FixedTimeProvider` no longer accepts an array but accepts only
  `Type\Time` instances.
* `Provider\NodeProviderInterface::getNode()` now returns `Type\Hexadecimal`
  instead of `string|false|null`.
* `Converter/TimeConverterInterface::calculateTime()` now returns
  `Type\Hexadecimal` instead of `array`. The value is the full UUID timestamp
  value (count of 100-nanosecond intervals since the Gregorian calendar epoch)
  in hexadecimal format.
* Change methods in `NumberConverterInterface` to accept and return string values
  instead of `mixed`; this simplifies the interface and makes it consistent.
* `Generator\DefaultTimeGenerator` no longer adds the variant and version bits
  to the bytes it returns. These must be applied to the bytes afterwards.
* When encoding to bytes or decoding from bytes, `OrderedTimeCodec` now checks
  whether the UUID is an RFC 4122 variant, version 1 UUID. If not, it will throw
  an exception—`InvalidArgumentException` when using
  `OrderedTimeCodec::encodeBinary()` and `UnsupportedOperationException` when
  using `OrderedTimeCodec::decodeBytes()`.

### Deprecated

The following functionality is deprecated and will be removed in ramsey/uuid
5.0.0.

* The following methods from `UuidInterface` and `Uuid` are deprecated. Use their
  counterparts on the `Rfc4122\FieldsInterface` returned by `Uuid::getFields()`.
  * `getClockSeqHiAndReservedHex()`
  * `getClockSeqLowHex()`
  * `getClockSequenceHex()`
  * `getFieldsHex()`
  * `getNodeHex()`
  * `getTimeHiAndVersionHex()`
  * `getTimeLowHex()`
  * `getTimeMidHex()`
  * `getTimestampHex()`
  * `getVariant()`
  * `getVersion()`
* The following methods from `Uuid` are deprecated. Use the `Rfc4122\FieldsInterface`
  instance returned by `Uuid::getFields()` to get the `Type\Hexadecimal` value
  for these fields. You may use the new `Math\CalculatorInterface::toIntegerValue()`
  method to convert the `Type\Hexadecimal` instances to instances of
  `Type\Integer`. This library provides `Math\BrickMathCalculator`, which may be
  used for this purpose, or you may use the arbitrary-precision arithemetic
  library of your choice.
  * `getClockSeqHiAndReserved()`
  * `getClockSeqLow()`
  * `getClockSequence()`
  * `getNode()`
  * `getTimeHiAndVersion()`
  * `getTimeLow()`
  * `getTimeMid()`
  * `getTimestamp()`
* `getDateTime()` on `UuidInterface` and `Uuid` is deprecated. Use this method
  only on instances of `Rfc4122\UuidV1` or `Nonstandard\UuidV6`.
* `getUrn()` on `UuidInterface` and `Uuid` is deprecated. It is available on
  `Rfc4122\UuidInterface` and classes that implement it.
* The following methods are deprecated and have no direct replacements. However,
  you may obtain the same information by calling `UuidInterface::getHex()` and
  splitting the return value in half.
  * `UuidInterface::getLeastSignificantBitsHex()`
  * `UuidInterface::getMostSignificantBitsHex()`
  * `Uuid::getLeastSignificantBitsHex()`
  * `Uuid::getMostSignificantBitsHex()`
  * `Uuid::getLeastSignificantBits()`
  * `Uuid::getMostSignificantBits()`
* `UuidInterface::getNumberConverter()` and `Uuid::getNumberConverter()` are
  deprecated. There is no alternative recommendation, so plan accordingly.
* `Builder\DefaultUuidBuilder` is deprecated; transition to `Rfc4122\UuidBuilder`.
* `Converter\Number\BigNumberConverter` is deprecated; transition to
  `Converter\Number\GenericNumberConverter`.
* `Converter\Time\BigNumberTimeConverter` is deprecated; transition to
  `Converter\Time\GenericTimeConverter`.
* The classes for representing and generating *degraded* UUIDs are deprecated.
  These are no longer necessary; this library now behaves the same on 32-bit and
  64-bit systems.
  * `Builder\DegradedUuidBuilder`
  * `Converter\Number\DegradedNumberConverter`
  * `Converter\Time\DegradedTimeConverter`
  * `DegradedUuid`
* The `Uuid::UUID_TYPE_IDENTIFIER` constant is deprecated. Use
  `Uuid::UUID_TYPE_DCE_SECURITY` instead.
* The `Uuid::VALID_PATTERN` constant is deprecated. Use
  `Validator\GenericValidator::getPattern()` or `Rfc4122\Validator::getPattern()`
  instead.

### Removed

* Remove the following bytes generators and recommend
  `Generator\RandomBytesGenerator` as a suitable replacement:
  * `Generator\MtRandGenerator`
  * `Generator\OpenSslGenerator`
  * `Generator\SodiumRandomGenerator`
* Remove `Exception\UnsatisfiedDependencyException`. This library no longer
  throws this exception.
* Remove the method `Provider\TimeProviderInterface::currentTime()`. Use
  `Provider\TimeProviderInterface::getTime()` instead.


## [4.0.0-beta2] - 2020-03-01

## Added

* Add missing convenience methods for `Rfc4122\UuidV2`.
* Add `Provider\Node\StaticNodeProvider` to assist in setting a custom static
  node value with the multicast bit set for version 1 UUIDs.

## Changed

* `Provider\NodeProviderInterface::getNode()` now returns `Type\Hexadecimal`
  instead of `string|false|null`.


## [4.0.0-beta1] - 2020-02-27

### Added

* Add `ValidatorInterface::getPattern()` to return the regular expression
  pattern used by the validator.
* Add `v6()` helper function for version 6 UUIDs.

### Changed

* Set the pattern constants on validators as `private`. Use the `getPattern()`
  method instead.
* Change the `$node` parameter for `UuidFactoryInterface::uuid6()` to accept
  `null` or `Type\Hexadecimal`.
* Accept `Type\Hexadecimal` for the `$node` parameter for
  `UuidFactoryInterface::uuid1()`. This is in addition to the `int|string` types
  already accepted, so there are no BC breaks. `Type\Hexadecimal` is now the
  recommended type to pass for `$node`.

### Removed

* Remove `currentTime()` method from `Provider\Time\FixedTimeProvider` and
  `Provider\Time\SystemTimeProvider`; it had previously been removed from
  `Provider\TimeProviderInterface`.


## [4.0.0-alpha5] - 2020-02-23

### Added

* Introduce `Builder\BuilderCollection` and `Provider\Node\NodeProviderCollection`.

### Changed

* `Builder\FallbackBuilder` now accepts only a `Builder\BuilderCollection` as
  its constructor parameter.
* `Provider\Node\FallbackNodeProvider` now accepts only a `Provider\Node\NodeProviderCollection`
  as its constructor parameter.
* `Provider\Time\FixedTimeProvider` no longer accepts an array but accepts only
  `Type\Time` instances.


## [4.0.0-alpha4] - 2020-02-23

### Added

* Add a `Type\TypeInterface` that each of the ramsey/uuid types implements.
* Support version 6 UUIDs; see <http://gh.peabody.io/uuidv6/>.

### Changed

* Rename `Type\IntegerValue` to `Type\Integer`. It was originally named
  `IntegerValue` because static analysis sees `Integer` in docblock annotations
  and treats it as the native `int` type. `Integer` is not a reserved word in
  PHP, so it should be named `Integer` for consistency with other types in this
  library. When using it, a class alias prevents static analysis from
  complaining.
* Mark `Guid\Guid` and `Nonstandard\Uuid` classes as `final`.
* Add `uuid6()` method to `UuidFactoryInterface`.

### Deprecated

* `Uuid::UUID_TYPE_IDENTIFIER` is deprecated. Use `Uuid::UUID_TYPE_DCE_SECURITY`
  instead.
* `Uuid::VALID_PATTERN` is deprecated. Use `Validator\GenericValidator::VALID_PATTERN`
  instead.


## [4.0.0-alpha3] - 2020-02-21

### Fixed

* Fix microsecond rounding error on 32-bit systems.


## [4.0.0-alpha2] - 2020-02-21

### Added

* Add `Uuid::fromDateTime()` to create version 1 UUIDs from instances of
  `\DateTimeInterface`.
* Add `Generator\NameGeneratorInterface` to support alternate methods of
  generating bytes for version 3 and version 5 name-based UUID. By default,
  ramsey/uuid uses the `Generator\DefaultNameGenerator`, which uses the standard
  algorithm this library has used since the beginning. You may choose to use the
  new `Generator\PeclUuidNameGenerator` to make use of the new
  `uuid_generate_md5()` and `uuid_generate_sha1()` functions in ext-uuid version
  1.1.0.

### Changed

* Add `fromDateTime()` method to `UuidFactoryInterface`.
* Change `UuidInterface::getHex()` to return a `Ramsey\Uuid\Type\Hexadecimal` instance.
* Change `UuidInterface::getInteger()` to return a `Ramsey\Uuid\Type\IntegerValue` instance.

### Fixed

* Round microseconds to six digits when getting DateTime from v1 UUIDs. This
  circumvents a needless exception for an otherwise valid time-based UUID.


## [4.0.0-alpha1] - 2020-01-22

### Added

* Add `Validator\ValidatorInterface` and `Validator\GenericValidator` to allow
  flexibility in validating UUIDs/GUIDs.
  * Add ability to change the default validator used by `Uuid` through
    `FeatureSet::setValidator()`.
  * Add `getValidator()` and `setValidator()` to `UuidFactory`.
* Add an internal `InvalidArgumentException` that descends from the built-in
  PHP `\InvalidArgumentException`. All places that used to throw
  `\InvalidArgumentException` now throw `Ramsey\Uuid\Exception\InvalidArgumentException`.
  This should not cause any BC breaks, however.
* Add an internal `DateTimeException` that descends from the built-in PHP
  `\RuntimeException`. `Uuid::getDateTime()` may throw this exception if
  `\DateTimeImmutable` throws an error or exception.
* Add `RandomSourceException` that descends from the built-in PHP
  `\RuntimeException`. `DefaultTimeGenerator`, `RandomBytesGenerator`, and
  `RandomNodeProvider` may throw this exception if `random_bytes()` or
  `random_int()` throw an error or exception.
* Add `Fields\FieldsInterface` and `Rfc4122\FieldsInterface` to define
  field layouts for UUID variants. The implementations `Rfc4122\Fields`,
  `Guid\Fields`, and `Nonstandard\Fields` store the 16-byte,
  binary string representation of the UUID internally, and these manage
  conversion of the binary string into the hexadecimal field values.
* Add `Rfc4122\UuidInterface` to specifically represent RFC 4122 variant UUIDs.
* Add classes to represent each version of RFC 4122 UUID. When generating new
  UUIDs or creating UUIDs from existing strings, bytes, or integers, if the UUID
  is an RFC 4122 variant, one of these instances will be returned:
  * `Rfc4122\UuidV1`
  * `Rfc4122\UuidV2`
  * `Rfc4122\UuidV3`
  * `Rfc4122\UuidV4`
  * `Rfc4122\UuidV5`
  * `Rfc4122\NilUuid`
* Add `Rfc4122\UuidBuilder` to build RFC 4122 variant UUIDs. This replaces the
  existing `Builder\DefaultUuidBuilder`, which is now deprecated.
* Add ability to generate version 2 (DCE Security) UUIDs, including the static
  method `Uuid::uuid2()`, which returns an `Rfc4122\UuidV2` instance.
* Add classes to represent GUIDs and nonstandard (non-RFC 4122 variant) UUIDs:
  * `Guid\Guid`
  * `Nonstandard\Uuid`.
* Introduce a `Builder\FallbackBuilder`, used by `FeatureSet` to help decide
  whether to return a `Uuid` or `Nonstandard\Uuid` when decoding a
  UUID string or bytes.
* Introduce `Type\Hexadecimal`, `Type\IntegerValue`, and `Type\Time` for
  improved type-safety when dealing with arbitrary string values.
* Introduce `Math\CalculatorInterface` for representing calculators to perform
  arithmetic operations on integers.
* Depend on [brick/math](https://github.com/brick/math) for the
  `Math\BrickMathCalculator`, which is the default calculator used by this
  library when math cannot be performed in native PHP due to integer size
  limitations. The calculator is configurable and may be changed, if desired.
* Add `Converter\Number\GenericNumberConverter` and
  `Converter\Time\GenericTimeConverter` which will use the calculator provided
  to convert numbers and time to values for UUIDs.
* The `\DateTimeInterface` instance returned by `UuidInterface::getDateTime()`
  (and now `Rfc4122\UuidV1::getDateTime()`) now includes microseconds, as
  specified by the version 1 UUID.

### Changed

* Set minimum required PHP version to 7.2.
* Add `__toString()` method to `UuidInterface`.
* The `UuidInterface::getDateTime()` method now specifies `\DateTimeInterface`
  as the return value, rather than `\DateTime`; `Uuid::getDateTime()` now
  returns an instance of `\DateTimeImmutable` instead of `\DateTime`.
* Add `getFields()` method to `UuidInterface`.
* Add `getValidator()` method to `UuidFactoryInterface`.
* Add `uuid2()` method to `UuidFactoryInterface`.
* Add `convertTime()` method to `Converter\TimeConverterInterface`.
* Add `getTime()` method to `Provider\TimeProviderInterface`.
* Change `Uuid::getFields()` to return an instance of `Fields\FieldsInterface`.
  Previously, it returned an array of integer values (on 64-bit systems only).
* Change the first required constructor parameter for `Uuid` from
  `array $fields` to `Rfc4122\FieldsInterface $fields`.
* Introduce `Converter\TimeConverterInterface $timeConverter` as fourth required
  constructor parameter for `Uuid` and second required constructor parameter for
  `Builder\DefaultUuidBuilder`.
* Change `UuidInterface::getInteger()` to always return a `string` value instead
  of `mixed`. This is a string representation of a 128-bit integer. You may then
  use a math library of your choice (bcmath, gmp, etc.) to operate on the
  string integer.
* Change the second required parameter of `Builder\UuidBuilderInterface::build()`
  from `array $fields` to `string $bytes`. Rather than accepting an array of
  hexadecimal strings as UUID fields, the `build()` method now expects a byte
  string.
* `Generator\DefaultTimeGenerator` no longer adds the variant and version bits
  to the bytes it returns. These must be applied to the bytes afterwards.
* `Converter/TimeConverterInterface::calculateTime()` now returns
  `Type\Hexadecimal` instead of `array`. The value is the full UUID timestamp
  value (count of 100-nanosecond intervals since the Gregorian calendar epoch)
  in hexadecimal format.
* Change methods in converter interfaces to accept and return string values
  instead of `mixed`; this simplifies the interface and makes it consistent:
  * `NumberConverterInterface::fromHex(string $hex): string`
  * `NumberConverterInterface::toHex(string $number): string`
  * `TimeConverterInterface::calculateTime(string $seconds, string $microseconds): array`
* `UnsupportedOperationException` is now descended from `\LogicException`.
  Previously, it descended from `\RuntimeException`.
* When encoding to bytes or decoding from bytes, `OrderedTimeCodec` now checks
  whether the UUID is an RFC 4122 variant, version 1 UUID. If not, it will throw
  an exception—`InvalidArgumentException` when using
  `OrderedTimeCodec::encodeBinary()` and `UnsupportedOperationException` when
  using `OrderedTimeCodec::decodeBytes()`.
* Out of the box, `Uuid::fromString()`, `Uuid::fromBytes()`, and
  `Uuid::fromInteger()` will now return either an `Rfc4122\UuidInterface`
  instance or an instance of `Nonstandard\Uuid`, depending on whether the input
  contains an RFC 4122 variant UUID with a valid version identifier. Both
  implement `UuidInterface`, so BC breaks should not occur if typehints use the
  interface.
* By default, the following static methods will now return the specific instance
  types. This should not cause any BC breaks if typehints target `UuidInterface`:
  * `Uuid::uuid1` returns `Rfc4122\UuidV1`
  * `Uuid::uuid3` returns `Rfc4122\UuidV3`
  * `Uuid::uuid4` returns `Rfc4122\UuidV4`
  * `Uuid::uuid5` returns `Rfc4122\UuidV5`

### Deprecated

The following functionality is deprecated and will be removed in ramsey/uuid
5.0.0.

* The following methods from `UuidInterface` and `Uuid` are deprecated. Use their
  counterparts on the `Rfc4122\FieldsInterface` returned by `Uuid::getFields()`.
  * `getClockSeqHiAndReservedHex()`
  * `getClockSeqLowHex()`
  * `getClockSequenceHex()`
  * `getFieldsHex()`
  * `getNodeHex()`
  * `getTimeHiAndVersionHex()`
  * `getTimeLowHex()`
  * `getTimeMidHex()`
  * `getTimestampHex()`
  * `getVariant()`
  * `getVersion()`
* The following methods from `Uuid` are deprecated. Use the `Rfc4122\FieldsInterface`
  instance returned by `Uuid::getFields()` to get the `Type\Hexadecimal` value
  for these fields, and then use the arbitrary-precision arithmetic library of
  your choice to convert them to string integers.
  * `getClockSeqHiAndReserved()`
  * `getClockSeqLow()`
  * `getClockSequence()`
  * `getNode()`
  * `getTimeHiAndVersion()`
  * `getTimeLow()`
  * `getTimeMid()`
  * `getTimestamp()`
* `getDateTime()` on `UuidInterface` and `Uuid` is deprecated. Use this method
  only on instances of `Rfc4122\UuidV1`.
* `getUrn()` on `UuidInterface` and `Uuid` is deprecated. It is available on
  `Rfc4122\UuidInterface` and classes that implement it.
* The following methods are deprecated and have no direct replacements. However,
  you may obtain the same information by calling `UuidInterface::getHex()` and
  splitting the return value in half.
  * `UuidInterface::getLeastSignificantBitsHex()`
  * `UuidInterface::getMostSignificantBitsHex()`
  * `Uuid::getLeastSignificantBitsHex()`
  * `Uuid::getMostSignificantBitsHex()`
  * `Uuid::getLeastSignificantBits()`
  * `Uuid::getMostSignificantBits()`
* `UuidInterface::getNumberConverter()` and `Uuid::getNumberConverter()` are
  deprecated. There is no alternative recommendation, so plan accordingly.
* `Builder\DefaultUuidBuilder` is deprecated; transition to
  `Rfc4122\UuidBuilder`.
* `Converter\Number\BigNumberConverter` is deprecated; transition to
  `Converter\Number\GenericNumberConverter`.
* `Converter\Time\BigNumberTimeConverter` is deprecated; transition to
  `Converter\Time\GenericTimeConverter`.
* `Provider\TimeProviderInterface::currentTime()` is deprecated; transition to
  the `getTimestamp()` method on the same interface.
* The classes for representing and generating *degraded* UUIDs are deprecated.
  These are no longer necessary; this library now behaves the same on 32-bit and
  64-bit PHP.
  * `Builder\DegradedUuidBuilder`
  * `Converter\Number\DegradedNumberConverter`
  * `Converter\Time\DegradedTimeConverter`
  * `DegradedUuid`

### Removed

* Remove the following bytes generators and recommend
  `Generator\RandomBytesGenerator` as a suitable replacement:
  * `Generator\MtRandGenerator`
  * `Generator\OpenSslGenerator`
  * `Generator\SodiumRandomGenerator`
* Remove `Exception\UnsatisfiedDependencyException`. This library no longer
  throws this exception.


## [3.9.3] - 2020-02-20

### Fixed

* For v1 UUIDs, round down for timestamps so that microseconds do not bump the
  timestamp to the next second.

  As an example, consider the case of timestamp `1` with  `600000` microseconds
  (`1.600000`). This is the first second after midnight on January 1, 1970, UTC.
  Previous versions of this library had a bug that would round this to `2`, so
  the rendered time was `1970-01-01 00:00:02`. This was incorrect. Despite
  having `600000` microseconds, the time should not round up to the next second.
  Rather, the time should be `1970-01-01 00:00:01.600000`. Since this version of
  ramsey/uuid does not support microseconds, the microseconds are dropped, and
  the time is `1970-01-01 00:00:01`. No rounding should occur.


## [3.9.2] - 2019-12-17

### Fixed

* Check whether files returned by `/sys/class/net/*/address` are readable
  before attempting to read them. This avoids a PHP warning that was being
  emitted on hosts that do not grant permission to read these files.


## [3.9.1] - 2019-12-01

### Fixed

* Fix `RandomNodeProvider` behavior on 32-bit systems. The `RandomNodeProvider`
  was converting a 6-byte string to a decimal number, which is a 48-bit,
  unsigned integer. This caused problems on 32-bit systems and has now been
  resolved.


## [3.9.0] - 2019-11-30

### Added

* Add function API as convenience. The functions are available in the
  `Ramsey\Uuid` namespace.
  * `v1(int|string|null $node = null, int|null $clockSeq = null): string`
  * `v3(string|UuidInterface $ns, string $name): string`
  * `v4(): string`
  * `v5(string|UuidInterface $ns, string $name): string`

### Changed

* Use paragonie/random-lib instead of ircmaxell/random-lib. This is a
  non-breaking change.
* Use a high-strength generator by default, when using `RandomLibAdapter`. This
  is a non-breaking change.

### Deprecated

These will be removed in ramsey/uuid version 4.0.0:

* `MtRandGenerator`, `OpenSslGenerator`, and `SodiumRandomGenerator` are
  deprecated in favor of using the default `RandomBytesGenerator`.

### Fixed

* Set `ext-json` as a required dependency in `composer.json`.
* Use `PHP_OS` instead of `php_uname()` when determining the system OS, for
  cases when `php_uname()` is disabled for security reasons.


## [3.8.0] - 2018-07-19

### Added

* Support discovery of MAC addresses on FreeBSD systems
* Use a polyfill to provide PHP ctype functions when running on systems where the
  ctype functions are not part of the PHP build
* Disallow a trailing newline character when validating UUIDs
* Annotate thrown exceptions for improved IDE hinting


## [3.7.3] - 2018-01-19

### Fixed

* Gracefully handle cases where `glob()` returns false when searching
  `/sys/class/net/*/address` files on Linux
* Fix off-by-one error in `DefaultTimeGenerator`

### Security

* Switch to `random_int()` from `mt_rand()` for better random numbers


## [3.7.2] - 2018-01-13

### Fixed

* Check sysfs on Linux to determine the node identifier; this provides a
  reliable way to identify the node on Docker images, etc.


## [3.7.1] - 2017-09-22

### Fixed

* Set the multicast bit for random nodes, according to RFC 4122, §4.5

### Security

* Use `random_bytes()` when generating random nodes


## [3.7.0] - 2017-08-04

### Added

* Add the following UUID version constants:
    * `Uuid::UUID_TYPE_TIME`
    * `Uuid::UUID_TYPE_IDENTIFIER`
    * `Uuid::UUID_TYPE_HASH_MD5`
    * `Uuid::UUID_TYPE_RANDOM`
    * `Uuid::UUID_TYPE_HASH_SHA1`


## [3.6.1] - 2017-03-26

### Fixed

* Optimize UUID string decoding by using `str_pad()` instead of `sprintf()`


## [3.6.0] - 2017-03-18

### Added

* Add `InvalidUuidStringException`, which is thrown when attempting to decode an
  invalid string UUID; this does not introduce any BC issues, since the new
  exception inherits from the previously used `InvalidArgumentException`

### Fixed

* Improve memory usage when generating large quantities of UUIDs (use `str_pad()`
  and `dechex()` instead of `sprintf()`)


## [3.5.2] - 2016-11-22

### Fixed

* Improve test coverage


## [3.5.1] - 2016-10-02

### Fixed

* Fix issue where the same UUIDs were not being treated as equal when using
  mixed cases


## [3.5.0] - 2016-08-02

### Added

* Add `OrderedTimeCodec` to store UUID in an optimized way for InnoDB

### Fixed

* Fix invalid node generation in `RandomNodeProvider`
* Avoid multiple unnecessary system calls by caching failed attempt to retrieve
  system node


## [3.4.1] - 2016-04-23

### Fixed

* Fix test that violated a PHP CodeSniffer rule, breaking the build


## [3.4.0] - 2016-04-23

### Added

* Add `TimestampFirstCombCodec` and `TimestampLastCombCodec` codecs to provide
  the ability to generate [COMB sequential UUIDs] with the timestamp encoded as
  either the first 48 bits or the last 48 bits
* Improve logic of `CombGenerator` for COMB sequential UUIDs


## [3.3.0] - 2016-03-22

### Security

* Drop the use of OpenSSL as a fallback and use [paragonie/random_compat] to
  support `RandomBytesGenerator` in versions of PHP earlier than 7.0;
  this addresses and fixes the [collision issue]


## [3.2.0] - 2016-02-17

### Added

* Add `SodiumRandomGenerator` to allow use of the [PECL libsodium extension] as
  a random bytes generator when creating UUIDs


## [3.1.0] - 2015-12-17

### Added

* Implement the PHP `Serializable` interface to provide the ability to
  serialize/unserialize UUID objects


## [3.0.1] - 2015-10-21

### Added

* Adopt the [Contributor Code of Conduct] for this project


## [3.0.0] - 2015-09-28

The 3.0.0 release represents a significant step for the ramsey/uuid library.
While the simple and familiar API used in previous versions remains intact, this
release provides greater flexibility to integrators, including the ability to
inject your own number generators, UUID codecs, node and time providers, and
more.

*Please note: The changelog for 3.0.0 includes all notes from the alpha and beta
versions leading up to this release.*

### Added

* Add a number of generators that may be used to override the library defaults
  for generating random bytes (version 4) or time-based (version 1) UUIDs
  * `CombGenerator` to allow generation of sequential UUIDs
  * `OpenSslGenerator` to generate random bytes on systems where
    `openssql_random_pseudo_bytes()` is present
  * `MtRandGenerator` to provide a fallback in the event other random generators
    are not present
  * `RandomLibAdapter` to allow use of [ircmaxell/random-lib]
  * `RandomBytesGenerator` for use with PHP 7; ramsey/uuid will default to use
    this generator when running on PHP 7
  * Refactor time-based (version 1) UUIDs into a `TimeGeneratorInterface` to
    allow for other sources to generate version 1 UUIDs in this library
  * `PeclUuidTimeGenerator` and `PeclUuidRandomGenerator` for creating version
    1 or version 4 UUIDs using the pecl-uuid extension
* Add a `setTimeGenerator` method on `UuidFactory` to override the default time
  generator
* Add option to enable `PeclUuidTimeGenerator` via `FeatureSet`
* Support GUID generation by configuring a `FeatureSet` to use GUIDs
* Allow UUIDs to be serialized as JSON through `JsonSerializable`

### Changed

* Change root namespace from "Rhumsaa" to "Ramsey;" in most cases, simply
  making this change in your applications is the only upgrade path you will
  need—everything else should work as expected
* No longer consider `Uuid` class as `final`; everything is now based around
  interfaces and factories, allowing you to use this package as a base to
  implement other kinds of UUIDs with different dependencies
* Return an object of type `DegradedUuid` on 32-bit systems to indicate that
  certain features are not available
* Default `RandomLibAdapter` to a medium-strength generator with
  [ircmaxell/random-lib]; this is configurable, so other generator strengths may
  be used

### Removed

* Remove `PeclUuidFactory` in favor of using pecl-uuid with generators
* Remove `timeConverter` and `timeProvider` properties, setters, and getters in
  both `FeatureSet` and `UuidFactory` as those are now exclusively used by the
  default `TimeGenerator`
* Move UUID [Doctrine field type] to [ramsey/uuid-doctrine]
* Move `uuid` console application to [ramsey/uuid-console]
* Remove `Uuid::VERSION` package version constant

### Fixed

* Improve GUID support to ensure that:
  * On little endian (LE) architectures, the byte order of the first three
    fields is LE
  * On big endian (BE) architectures, it is the same as a GUID
  * String representation is always the same
* Fix exception message for `DegradedNumberConverter::fromHex()`


## [3.0.0-beta1] - 2015-08-31

### Fixed

* Improve GUID support to ensure that:
  * On little endian (LE) architectures, the byte order of the first three
    fields is LE
  * On big endian (BE) architectures, it is the same as a GUID
  * String representation is always the same
* Fix exception message for `DegradedNumberConverter::fromHex()`


## [3.0.0-alpha3] - 2015-07-28

### Added

* Enable use of custom `TimeGenerator` implementations
* Add a `setTimeGenerator` method on `UuidFactory` to override the default time
  generator
* Add option to enable `PeclUuidTimeGenerator` via `FeatureSet`

### Removed

* Remove `timeConverter` and `timeProvider` properties, setters, and getters in
  both `FeatureSet` and `UuidFactory` as those are now exclusively used by the
  default `TimeGenerator`


## [3.0.0-alpha2] - 2015-07-28

### Added

* Refactor time-based (version 1) UUIDs into a `TimeGeneratorInterface` to allow
  for other sources to generate version 1 UUIDs in this library
* Add `PeclUuidTimeGenerator` and `PeclUuidRandomGenerator` for creating version
  1 or version 4 UUIDs using the pecl-uuid extension
* Add `RandomBytesGenerator` for use with PHP 7. ramsey/uuid will default to use
  this generator when running on PHP 7

### Changed

* Default `RandomLibAdapter` to a medium-strength generator with
  [ircmaxell/random-lib]; this is configurable, so other generator strengths may
  be used

### Removed

* Remove `PeclUuidFactory` in favor of using pecl-uuid with generators


## [3.0.0-alpha1] - 2015-07-16

### Added

* Allow dependency injection through `UuidFactory` and/or extending `FeatureSet`
  to override any package defaults
* Add a number of generators that may be used to override the library defaults:
  * `CombGenerator` to allow generation of sequential UUIDs
  * `OpenSslGenerator` to generate random bytes on systems where
    `openssql_random_pseudo_bytes()` is present
  * `MtRandGenerator` to provide a fallback in the event other random generators
    are not present
  * `RandomLibAdapter` to allow use of [ircmaxell/random-lib]
* Support GUID generation by configuring a `FeatureSet` to use GUIDs
* Allow UUIDs to be serialized as JSON through `JsonSerializable`

### Changed

* Change root namespace from "Rhumsaa" to "Ramsey;" in most cases, simply
  making this change in your applications is the only upgrade path you will
  need—everything else should work as expected
* No longer consider `Uuid` class as `final`; everything is now based around
  interfaces and factories, allowing you to use this package as a base to
  implement other kinds of UUIDs with different dependencies
* Return an object of type `DegradedUuid` on 32-bit systems to indicate that
  certain features are not available

### Removed

* Move UUID [Doctrine field type] to [ramsey/uuid-doctrine]
* Move `uuid` console application to [ramsey/uuid-console]
* Remove `Uuid::VERSION` package version constant


## [2.9.0] - 2016-03-22

### Security

* Drop the use of OpenSSL as a fallback and use [paragonie/random_compat] to
  support `RandomBytesGenerator` in versions of PHP earlier than 7.0;
  this addresses and fixes the [collision issue]


## [2.8.4] - 2015-12-17

### Added

* Add support for symfony/console v3 in the `uuid` CLI application


## [2.8.3] - 2015-08-31

### Fixed

* Fix exception message in `Uuid::calculateUuidTime()`


## [2.8.2] - 2015-07-23

### Fixed

* Ensure the release tag makes it into the rhumsaa/uuid package


## [2.8.1] - 2015-06-16

### Fixed

* Use `passthru()` and output buffering in `getIfconfig()`
* Cache the system node in a static variable so that we process it only once per
  runtime


## [2.8.0] - 2014-11-09

### Added

* Add static `fromInteger()` method to create UUIDs from string integer or
  `Moontoast\Math\BigNumber`

### Fixed

* Improve Doctrine conversion to Uuid or string for the ramsey/uuid [Doctrine field type]


## [2.7.4] - 2014-10-29

### Fixed

* Change loop in `generateBytes()` from `foreach` to `for`


## [2.7.3] - 2014-08-27

### Fixed

* Fix upper range for `mt_rand` used in version 4 UUIDs


## [2.7.2] - 2014-07-28

### Changed

* Upgrade to PSR-4 autoloading


## [2.7.1] - 2014-02-19

### Fixed

* Move moontoast/math and symfony/console to require-dev
* Support symfony/console 2.3 (LTS version)


## [2.7.0] - 2014-01-31

### Added

* Add `Uuid::VALID_PATTERN` constant containing a UUID validation regex pattern


## [2.6.1] - 2014-01-27

### Fixed

* Fix bug where `uuid` console application could not find the Composer
  autoloader when installed in another project


## [2.6.0] - 2014-01-17

### Added

* Introduce `uuid` console application for generating and decoding UUIDs from
  CLI (run `./bin/uuid` for details)
* Add `Uuid::getInteger()` to retrieve a `Moontoast\Math\BigNumber`
  representation of the 128-bit integer representing the UUID
* Add `Uuid::getHex()` to retrieve the hexadecimal representation of the UUID
* Use `netstat` on Linux to capture the node for a version 1 UUID
* Require moontoast/math as part of the regular package requirements


## [2.5.0] - 2013-10-30

### Added

* Use `openssl_random_pseudo_bytes()`, if available, to generate random bytes


## [2.4.0] - 2013-07-29

### Added

* Return `null` from `Uuid::getVersion()` if the UUID isn't an RFC 4122 variant
* Support string UUIDs without dashes passed to `Uuid::fromString()`


## [2.3.0] - 2013-07-16

### Added

* Support creation of UUIDs from bytes with `Uuid::fromBytes()`


## [2.2.0] - 2013-07-04

### Added

* Add `Doctrine\UuidType::requiresSQLCommentHint()` method


## [2.1.2] - 2013-07-03

### Fixed

* Fix cases where the system node was coming back with uppercase hexadecimal
  digits; this ensures that case in the node is converted to lowercase


## [2.1.1] - 2013-04-29

### Fixed

* Fix bug in `Uuid::isValid()` where the NIL UUID was not reported as valid


## [2.1.0] - 2013-04-15

### Added

* Allow checking the validity of a UUID through the `Uuid::isValid()` method


## [2.0.0] - 2013-02-11

### Added

* Support UUID generation on 32-bit platforms

### Changed

* Mark `Uuid` class `final`
* Require moontoast/math on 64-bit platforms for
  `Uuid::getLeastSignificantBits()` and `Uuid::getMostSignificantBits()`; the
  integers returned by these methods are *unsigned* 64-bit integers and
  unsupported even on 64-bit builds of PHP
* Move `UnsupportedOperationException` to the `Exception` subnamespace


## [1.1.2] - 2012-11-29

### Fixed

* Relax [Doctrine field type] conversion rules for UUIDs


## [1.1.1] - 2012-08-27

### Fixed

* Remove `final` keyword from `Uuid` class


## [1.1.0] - 2012-08-06

### Added

* Support ramsey/uuid UUIDs as a Doctrine Database Abstraction Layer (DBAL)
  field mapping type


## [1.0.0] - 2012-07-19

### Added

* Support generation of version 1, 3, 4, and 5 UUIDs


[comb sequential uuids]: http://www.informit.com/articles/article.aspx?p=25862&seqNum=7
[paragonie/random_compat]: https://github.com/paragonie/random_compat
[collision issue]: https://github.com/ramsey/uuid/issues/80
[contributor code of conduct]: https://github.com/ramsey/uuid/blob/master/.github/CODE_OF_CONDUCT.md
[pecl libsodium extension]: http://pecl.php.net/package/libsodium
[ircmaxell/random-lib]: https://github.com/ircmaxell/RandomLib
[doctrine field type]: http://doctrine-dbal.readthedocs.org/en/latest/reference/types.html
[ramsey/uuid-doctrine]: https://github.com/ramsey/uuid-doctrine
[ramsey/uuid-console]: https://github.com/ramsey/uuid-console

[unreleased]: https://github.com/ramsey/uuid/compare/4.0.1...HEAD
[4.0.1]: https://github.com/ramsey/uuid/compare/4.0.0...4.0.1
[4.0.0]: https://github.com/ramsey/uuid/compare/4.0.0-beta2...4.0.0
[4.0.0-beta2]: https://github.com/ramsey/uuid/compare/4.0.0-beta1...4.0.0-beta2
[4.0.0-beta1]: https://github.com/ramsey/uuid/compare/4.0.0-alpha5...4.0.0-beta1
[4.0.0-alpha5]: https://github.com/ramsey/uuid/compare/4.0.0-alpha4...4.0.0-alpha5
[4.0.0-alpha4]: https://github.com/ramsey/uuid/compare/4.0.0-alpha3...4.0.0-alpha4
[4.0.0-alpha3]: https://github.com/ramsey/uuid/compare/4.0.0-alpha2...4.0.0-alpha3
[4.0.0-alpha2]: https://github.com/ramsey/uuid/compare/4.0.0-alpha1...4.0.0-alpha2
[4.0.0-alpha1]: https://github.com/ramsey/uuid/compare/3.9.3...4.0.0-alpha1
[3.9.3]: https://github.com/ramsey/uuid/compare/3.9.2...3.9.3
[3.9.2]: https://github.com/ramsey/uuid/compare/3.9.1...3.9.2
[3.9.1]: https://github.com/ramsey/uuid/compare/3.9.0...3.9.1
[3.9.0]: https://github.com/ramsey/uuid/compare/3.8.0...3.9.0
[3.8.0]: https://github.com/ramsey/uuid/compare/3.7.3...3.8.0
[3.7.3]: https://github.com/ramsey/uuid/compare/3.7.2...3.7.3
[3.7.2]: https://github.com/ramsey/uuid/compare/3.7.1...3.7.2
[3.7.1]: https://github.com/ramsey/uuid/compare/3.7.0...3.7.1
[3.7.0]: https://github.com/ramsey/uuid/compare/3.6.1...3.7.0
[3.6.1]: https://github.com/ramsey/uuid/compare/3.6.0...3.6.1
[3.6.0]: https://github.com/ramsey/uuid/compare/3.5.2...3.6.0
[3.5.2]: https://github.com/ramsey/uuid/compare/3.5.1...3.5.2
[3.5.1]: https://github.com/ramsey/uuid/compare/3.5.0...3.5.1
[3.5.0]: https://github.com/ramsey/uuid/compare/3.4.1...3.5.0
[3.4.1]: https://github.com/ramsey/uuid/compare/3.4.0...3.4.1
[3.4.0]: https://github.com/ramsey/uuid/compare/3.3.0...3.4.0
[3.3.0]: https://github.com/ramsey/uuid/compare/3.2.0...3.3.0
[3.2.0]: https://github.com/ramsey/uuid/compare/3.1.0...3.2.0
[3.1.0]: https://github.com/ramsey/uuid/compare/3.0.1...3.1.0
[3.0.1]: https://github.com/ramsey/uuid/compare/3.0.0...3.0.1
[3.0.0]: https://github.com/ramsey/uuid/compare/3.0.0-beta1...3.0.0
[3.0.0-beta1]: https://github.com/ramsey/uuid/compare/3.0.0-alpha3...3.0.0-beta1
[3.0.0-alpha3]: https://github.com/ramsey/uuid/compare/3.0.0-alpha2...3.0.0-alpha3
[3.0.0-alpha2]: https://github.com/ramsey/uuid/compare/3.0.0-alpha1...3.0.0-alpha2
[3.0.0-alpha1]: https://github.com/ramsey/uuid/compare/2.9.0...3.0.0-alpha1
[2.9.0]: https://github.com/ramsey/uuid/compare/2.8.4...2.9.0
[2.8.4]: https://github.com/ramsey/uuid/compare/2.8.3...2.8.4
[2.8.3]: https://github.com/ramsey/uuid/compare/2.8.2...2.8.3
[2.8.2]: https://github.com/ramsey/uuid/compare/2.8.1...2.8.2
[2.8.1]: https://github.com/ramsey/uuid/compare/2.8.0...2.8.1
[2.8.0]: https://github.com/ramsey/uuid/compare/2.7.4...2.8.0
[2.7.4]: https://github.com/ramsey/uuid/compare/2.7.3...2.7.4
[2.7.3]: https://github.com/ramsey/uuid/compare/2.7.2...2.7.3
[2.7.2]: https://github.com/ramsey/uuid/compare/2.7.1...2.7.2
[2.7.1]: https://github.com/ramsey/uuid/compare/2.7.0...2.7.1
[2.7.0]: https://github.com/ramsey/uuid/compare/2.6.1...2.7.0
[2.6.1]: https://github.com/ramsey/uuid/compare/2.6.0...2.6.1
[2.6.0]: https://github.com/ramsey/uuid/compare/2.5.0...2.6.0
[2.5.0]: https://github.com/ramsey/uuid/compare/2.4.0...2.5.0
[2.4.0]: https://github.com/ramsey/uuid/compare/2.3.0...2.4.0
[2.3.0]: https://github.com/ramsey/uuid/compare/2.2.0...2.3.0
[2.2.0]: https://github.com/ramsey/uuid/compare/2.1.2...2.2.0
[2.1.2]: https://github.com/ramsey/uuid/compare/2.1.1...2.1.2
[2.1.1]: https://github.com/ramsey/uuid/compare/2.1.0...2.1.1
[2.1.0]: https://github.com/ramsey/uuid/compare/2.0.0...2.1.0
[2.0.0]: https://github.com/ramsey/uuid/compare/1.1.2...2.0.0
[1.1.2]: https://github.com/ramsey/uuid/compare/1.1.1...1.1.2
[1.1.1]: https://github.com/ramsey/uuid/compare/1.1.0...1.1.1
[1.1.0]: https://github.com/ramsey/uuid/compare/1.0.0...1.1.0
[1.0.0]: https://github.com/ramsey/uuid/commits/1.0.0
