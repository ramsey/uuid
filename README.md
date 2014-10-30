# Rhumsaa\Uuid for PHP

[![Build Status](https://travis-ci.org/ramsey/uuid.png?branch=master)](https://travis-ci.org/ramsey/uuid)
[![Coverage Status](https://coveralls.io/repos/ramsey/uuid/badge.png)](https://coveralls.io/r/ramsey/uuid)
[![Latest Stable Version](https://poser.pugx.org/rhumsaa/uuid/v/stable.png)](https://packagist.org/packages/rhumsaa/uuid)
[![Latest Unstable Version](https://poser.pugx.org/rhumsaa/uuid/v/unstable.png)](https://packagist.org/packages/rhumsaa/uuid)
[![Total Downloads](https://poser.pugx.org/rhumsaa/uuid/downloads.png)](https://packagist.org/packages/rhumsaa/uuid)
[![HHVM Status](http://hhvm.h4cc.de/badge/rhumsaa/uuid.png)](http://hhvm.h4cc.de/package/rhumsaa/uuid)

## About

Rhumsaa\Uuid is a PHP 5.3+ library for generating and working with
[RFC 4122][rfc4122] version 1, 3, 4, and 5 universally unique identifiers (UUID).

From [Wikipedia](http://en.wikipedia.org/wiki/Universally_unique_identifier):

> The intent of UUIDs is to enable distributed systems to uniquely identify
> information without significant central coordination. In this context the word
> unique should be taken to mean "practically unique" rather than "guaranteed
> unique". Since the identifiers have a finite size, it is possible for two
> differing items to share the same identifier. The identifier size and
> generation process need to be selected so as to make this sufficiently
> improbable in practice. Anyone can create a UUID and use it to identify
> something with reasonable confidence that the same identifier will never be
> unintentionally created by anyone to identify something else. Information
> labeled with UUIDs can therefore be later combined into a single database
> without needing to resolve identifier (ID) conflicts.

Much inspiration for this library came from the [Java][javauuid] and
[Python][pyuuid] UUID libraries.

## API Documentation

The [latest class API documentation][apidocs] is available online. This project
uses [ApiGen](http://apigen.org/) to generate this documentation. To
generate the documentation on your own, run `apigen` from the root of the project.
This will generate documentation in the `build/apidocs/` folder.

## Requirements

Some methods in this library have requirements due to integer size restrictions
on 32-bit and 64-bit builds of PHP. A 64-bit build of PHP and the [Moontoast\Math][]
library are recommended. However, this library is designed to work on 32-bit
builds of PHP without Moontoast\Math, with some degraded functionality. Please
check the API documentation for more information.

If a particular requirement is not present, then a `Rhumsaa\Uuid\Exception\UnsatisfiedDependencyException`
is thrown, allowing one to catch a bad call in an environment where the call is
not supported and gracefully degrade.

## Examples

```php
<?php
require 'vendor/autoload.php';

use Rhumsaa\Uuid\Uuid;
use Rhumsaa\Uuid\Exception\UnsatisfiedDependencyException;

try {

    // Generate a version 1 (time-based) UUID object
    $uuid1 = Uuid::uuid1();
    echo $uuid1->toString() . "\n"; // e4eaaaf2-d142-11e1-b3e4-080027620cdd

    // Generate a version 3 (name-based and hashed with MD5) UUID object
    $uuid3 = Uuid::uuid3(Uuid::NAMESPACE_DNS, 'php.net');
    echo $uuid3->toString() . "\n"; // 11a38b9a-b3da-360f-9353-a5a725514269

    // Generate a version 4 (random) UUID object
    $uuid4 = Uuid::uuid4();
    echo $uuid4->toString() . "\n"; // 25769c6c-d34d-4bfe-ba98-e0ee856f3e7a

    // Generate a version 5 (name-based and hashed with SHA1) UUID object
    $uuid5 = Uuid::uuid5(Uuid::NAMESPACE_DNS, 'php.net');
    echo $uuid5->toString() . "\n"; // c4a760a8-dbcf-5254-a0d9-6a4474bd1b62

} catch (UnsatisfiedDependencyException $e) {

    // Some dependency was not met. Either the method cannot be called on a
    // 32-bit system, or it can, but it relies on Moontoast\Math to be present.
    echo 'Caught exception: ' . $e->getMessage() . "\n";

}
```

## Installation

The preferred method of installation is via [Packagist][] and [Composer][]. Run
the following command to install the package and add it as a requirement to
`composer.json`:

```bash
composer.phar require "rhumsaa/uuid=~2.8"
```


[rfc4122]: http://tools.ietf.org/html/rfc4122
[javauuid]: http://docs.oracle.com/javase/6/docs/api/java/util/UUID.html
[pyuuid]: http://docs.python.org/3/library/uuid.html
[packagist]: https://packagist.org/packages/rhumsaa/uuid
[composer]: http://getcomposer.org/
[moontoast\math]: https://github.com/moontoast/math
[apidocs]: http://ramsey.github.io/uuid/apidocs
