# Rhumsaa\Uuid

[![Build Status](https://secure.travis-ci.org/ramsey/uuid.png)](http://travis-ci.org/ramsey/uuid)

A PHP 5.3+ library for generating and working with [RFC 4122][rfc4122] version
1, 3, 4, and 5 universally unique identifiers (UUID).

Much inspiration for this library came from the [Java][javauuid] and
[Python][pyuuid] UUID libraries.

## Requirements

Rhumsaa\Uuid works on 64-bit builds of PHP 5.3.3+.

Since, this library deals with large integers, so you will need to run it on a
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
