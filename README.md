# Rhumsaa\Uuid

[![Build Status](https://secure.travis-ci.org/ramsey/uuid.png)](http://travis-ci.org/ramsey/uuid)

A PHP 5.3+ library for generating and working with [RFC 4122][rfc4122] version
1, 3, 4, and 5 universally unique identifiers (UUID).

Much inspiration for this library came from the [Java][javauuid] and
[Python][pyuuid] UUID libraries.

## Requirements

*Rhumsaa\Uuid works on __64-bit builds__ of PHP 5.3.3+.*

This library deals with large integers, so you will need to run it on a
64-bit system with a 64-bit compiled version of PHP.

**Warning:** The [Windows binaries located on PHP.net][phpwin] are 32-bit
versions of PHP. Even if you run them on a 64-bit version of Windows, this
library will not work. You will need to compile PHP on Windows yourself to
build a 64-bit version.

## Examples

```php
<?php
use Rhumsaa\Uuid\Uuid;

// Generate a version 1 (time-based) UUID
$uuid1 = Uuid::uuid1();
echo $uuid1 . "\n"; // e4eaaaf2-d142-11e1-b3e4-080027620cdd

// Generate a version 3 (name-based and hashed with MD5) UUID
$uuid3 = Uuid::uuid3(Uuid::NAMESPACE_DNS, 'php.net');
echo $uuid3 . "\n"; // 11a38b9a-b3da-360f-9353-a5a725514269

// Generate a version 4 (random) UUID
$uuid4 = Uuid::uuid4();
echo $uuid4 . "\n"; // 25769c6c-d34d-4bfe-ba98-e0ee856f3e7a

// Generate a version 5 (name-based and hashed with SHA1) UUID
$uuid5 = Uuid::uuid5(Uuid::NAMESPACE_DNS, 'php.net');
echo $uuid5 . "\n"; // c4a760a8-dbcf-5254-a0d9-6a4474bd1b62
```

## Library API

### Creation methods

Each of the following methods are called statically to create Uuid objects. The
class constructor cannot be used to create Uuid objects. That is, the use of
`new Uuid()` is disabled.

Uuid **Uuid::fromString($name)**  
Creates a UUID from the string standard representation

Example:

    $uuid = Uuid::fromString('fa018870-d1f6-11e1-9b23-0800200c9a66');

Uuid **Uuid::uuid1()**  
Creates a time-based UUID (version 1)

Uuid **Uuid::uuid3($namespace, $name)**  
Creates a UUID (version 3) based on the MD5 hash of a namespace identifier and a name

Uuid **Uuid::uuid4()**
Creates a random UUID (version 4)

Uuid **Uuid::uuid5($namespace, $name)**  
Creates a UUID (version 5) based on the SHA-1 hash of a namespace identifier and a name

### Instance methods

Once you have a Uuid object, you may call the following methods on the object.

int **compareTo(Uuid $uuid)**  
Compares this UUID with the specified UUID. The first of two UUIDs is greater
than the second if the most significant field in which the UUIDs differ is
greater for the first UUID.

Returns -1 if this UUID is less than the compared to UUID, 0 if it is equal to
the compared to UUID, and 1 if it is greater than the compared to UUID.

Example:

    $uuid1 = Uuid::fromString('44cca71e-d13d-11e1-a959-c8bcc8a476f4');
    $uuid2 = Uuid::fromString('44cca71e-d13d-11e2-a959-c8bcc8a476f4');
    switch ($uuid1->$compareTo($uuid2)) {
        case -1:
            echo "$uuid1 is less than $uuid2";
            break;
        case 1:
            echo "$uuid1 is greater than $uuid2";
            break;
        case 0:
        default:
            echo "$uuid1 is equal to $uuid2";
    }

bool **equals($obj)**  
Compares this UUID to the specified object and returns `true` if they are equal.

string **getBytes()**  
Returns the UUID as a 16-byte string.

int **getClockSeqHiAndReserved()**  
Returns the high field of the clock sequence multiplexed with the variant
(bits 65-72 of the UUID).

int **getClockSeqLow()**  
Returns the low field of the clock sequence (bits 73-80 of the UUID).

int **getClockSequence()**  
Returns the full clock sequence, including the high and low fields.

\DateTime **getDateTime()**  
For version 1 UUIDs, this returns a PHP DateTime object representing the date
and time used to create the UUID.

array **getFields()**  
Returns an array of the fields of the UUID, with keys named according to the
RFC 4122 names for the fields.

| Field                      | Meaning                         |
| -------------------------- | ------------------------------- |
| time_low                   | the first 32 bits of the UUID   |
| time_mid                   | the next 16 bits of the UUID    |
| time_hi_and_version        | the next 16 bits of the UUID    |
| clock_seq_hi_and_reserved  | the next 8 bits of the UUID     |
| clock_seq_low              | the next 8 bits of the UUID     |
| node                       | the last 48 bits of the UUID    |

int **getLeastSignificantBits()**  
Returns the least significant 64 bits of this UUID's 128 bit value.

int **getMostSignificantBits()**  
Returns the most significant 64 bits of this UUID's 128 bit value.

int **getNode()**  
Returns the node value associated with this UUID.

int **getTimeHiAndVersion()**  
Returns the high field of the timestamp multiplexed with the version number
(bits 49-64 of the UUID).

int **getTimeLow()**  
Returns the low field of the timestamp (the first 32 bits of the UUID).

int **getTimeMid()**  
Returns the middle field of the timestamp (bits 33-48 of the UUID).

int **getTimestamp()**  
For version 1 UUIDs, this returns the 60 bit timestamp value used to create
this UUID. The timestamp is measured in 100-nanosecond units since midnight,
October 15, 1582, UTC. It is not a Unix timestamp.

string **getUrn()**  
Returns the string representation of the UUID as a URN.

int **getVariant()**  
Returns the variant number associated with this UUID.

The variant number has the following meaning:

* 0 - Reserved for NCS backward compatibility
* 2 - The RFC 4122 variant (used by this class
* 6 - Reserved, Microsoft Corporation backward compatibility
* 7 - Reserved for future definition

int **getVersion()**  
The version number associated with this UUID. The version number describes how
this UUID was generated.

The version number has the following meaning:

* 1 - Time-based UUID
* 2 - DCE security UUID
* 3 - Name-based UUID hashed with MD5
* 4 - Randomly generated UUID
* 5 - Name-based UUID hashed with SHA-1

string **toString()**  
Converts this UUID into a string representation. This class also implements
__toString(), which will convert this object to a string when it is used in
any string context.


## Installation

The preferred method of installation is via [Packagist][], as this provides
the PSR-0 autoloader functionality. The following `composer.json` will download
and install the latest version of the Uuid library into your project:

```json
{
    "require": {
        "rhumsaa/uuid": "*"
    }
}
```


[rfc4122]: http://tools.ietf.org/html/rfc4122
[javauuid]: http://docs.oracle.com/javase/6/docs/api/java/util/UUID.html
[pyuuid]: http://docs.python.org/library/uuid.html
[phpwin]: http://windows.php.net/download/
[packagist]: http://packagist.org/
