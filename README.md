<h1 align="center">ramsey/uuid</h1>

<p align="center">
    <strong>A PHP library for generating and working with UUIDs.</strong>
</p>

<p align="center">
    <a href="https://github.com/ramsey/uuid"><img src="http://img.shields.io/badge/source-ramsey/uuid-blue.svg?style=flat-square" alt="Source Code"></a>
    <a href="https://packagist.org/packages/ramsey/uuid"><img src="https://img.shields.io/packagist/v/ramsey/uuid.svg?style=flat-square&label=release" alt="Download Package"></a>
    <a href="https://php.net"><img src="https://img.shields.io/packagist/php-v/ramsey/uuid.svg?style=flat-square&colorB=%238892BF" alt="PHP Programming Language"></a>
    <a href="https://github.com/ramsey/uuid/actions?query=workflow%3ACI"><img src="https://img.shields.io/github/workflow/status/ramsey/uuid/CI?label=CI&logo=github&style=flat-square" alt="Build Status"></a>
    <a href="https://codecov.io/gh/ramsey/uuid"><img src="https://img.shields.io/codecov/c/gh/ramsey/uuid?label=codecov&logo=codecov&style=flat-square" alt="Codecov Code Coverage"></a>
    <a href="https://shepherd.dev/github/ramsey/uuid"><img src="https://img.shields.io/endpoint?style=flat-square&url=https%3A%2F%2Fshepherd.dev%2Fgithub%2Framsey%2Fuuid%2Fcoverage" alt="Psalm Type Coverage"></a>
    <a href="https://github.com/ramsey/uuid/blob/master/LICENSE"><img src="https://img.shields.io/packagist/l/ramsey/uuid.svg?style=flat-square&colorB=darkcyan" alt="Read License"></a>
    <a href="https://packagist.org/packages/ramsey/uuid/stats"><img src="https://img.shields.io/packagist/dt/ramsey/uuid.svg?style=flat-square&colorB=darkmagenta" alt="Package downloads on Packagist"></a>
    <a href="https://phpc.chat/channel/ramsey"><img src="https://img.shields.io/badge/phpc.chat-%23ramsey-darkslateblue?style=flat-square" alt="Chat with the maintainers"></a>
</p>

ramsey/uuid is a PHP library for generating and working with universally unique
identifiers (UUIDs).

This project adheres to a [Contributor Code of Conduct][conduct]. By
participating in this project and its community, you are expected to uphold this
code.

Much inspiration for this library came from the [Java][javauuid] and
[Python][pyuuid] UUID libraries.


## Installation

The preferred method of installation is via [Composer][]. Run the following
command to install the package and add it as a requirement to your project's
`composer.json`:

```bash
composer require ramsey/uuid
```


## Upgrading to Version 4

See the documentation for a thorough upgrade guide:

* [Upgrading ramsey/uuid Version 3 to 4](https://uuid.ramsey.dev/en/latest/upgrading/3-to-4.html)


## Documentation

Please see <https://uuid.ramsey.dev> for documentation, tips, examples, and
frequently asked questions.


## Contributing

Contributions are welcome! Please read [CONTRIBUTING.md][] for details.


## Copyright and License

The ramsey/uuid library is copyright © [Ben Ramsey](https://benramsey.com/) and
licensed for use under the MIT License (MIT). Please see [LICENSE][] for more
information.


[rfc4122]: http://tools.ietf.org/html/rfc4122
[conduct]: https://github.com/ramsey/uuid/blob/master/.github/CODE_OF_CONDUCT.md
[javauuid]: http://docs.oracle.com/javase/6/docs/api/java/util/UUID.html
[pyuuid]: http://docs.python.org/3/library/uuid.html
[composer]: http://getcomposer.org/
[contributing.md]: https://github.com/ramsey/uuid/blob/master/.github/CONTRIBUTING.md
