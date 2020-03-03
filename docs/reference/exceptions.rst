.. _reference.exceptions:

==========
Exceptions
==========

.. php:namespace:: Ramsey\Uuid\Exception

.. php:exception:: BuilderNotFoundException

    Extends ``\RuntimeException``.

    Thrown to indicate that no suitable UUID builder could be found.

.. php:exception:: DateTimeException

    Extends ``\RuntimeException``.

    Thrown to indicate that the PHP DateTime extension encountered an
    exception or error.

.. php:exception:: DceSecurityException

    Extends ``\RuntimeException``.

    Thrown to indicate an exception occurred while dealing with DCE Security
    (version 2) UUIDs

.. php:exception:: InvalidArgumentException

    Extends ``\InvalidArgumentException``.

    Thrown to indicate that the argument received is not valid.

.. php:exception:: InvalidBytesException

    Extends ``\RuntimeException``.

    Thrown to indicate that the bytes being operated on are invalid in some way.

.. php:exception:: InvalidUuidStringException

    Extends :php:exc:`Ramsey\\Uuid\\Exception\\InvalidArgumentException`.

    Thrown to indicate that the string received is not a valid UUID.

.. php:exception:: NameException

    Extends ``\RuntimeException``.

    Thrown to indicate that an error occurred while attempting to hash a
    namespace and name

.. php:exception:: NodeException

    Extends ``\RuntimeException``.

    Thrown to indicate that attempting to fetch or create a node ID encountered
    an error.

.. php:exception:: RandomSourceException

    Extends ``\RuntimeException``.

    Thrown to indicate that the source of random data encountered an error.

.. php:exception:: TimeSourceException

    Extends ``\RuntimeException``.

    Thrown to indicate that the source of time encountered an error.

.. php:exception:: UnableToBuildUuidException

    Extends ``\RuntimeException``.

    Thrown to indicate a builder is unable to build a UUID.

.. php:exception:: UnsupportedOperationException

    Extends ``\LogicException``.

    Thrown to indicate that the requested operation is not supported.
