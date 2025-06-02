.. _reference.types:

=====
Types
=====

.. php:namespace:: Ramsey\Uuid\Type

.. php:class:: TypeInterface

    Implements `JsonSerializable <https://www.php.net/jsonserializable>`_ and `Serializable <https://www.php.net/serializable>`_.

    TypeInterface ensures consistency in typed values returned by ramsey/uuid.

    .. php:method:: toString()

        :returntype: ``string``

    .. php:method:: __toString()

        :returntype: ``string``

.. php:class:: NumberInterface

    Implements :php:interface:`Ramsey\\Uuid\\Type\\TypeInterface`.

    NumberInterface ensures consistency in numeric values returned by ramsey/uuid.

    .. php:method:: isNegative()

        :returns: True if this number is less than zero, false otherwise.
        :returntype: ``bool``

.. php:class:: Decimal

    Implements :php:interface:`Ramsey\\Uuid\\Type\\NumberInterface`.

    A value object representing a decimal, for type-safety purposes, to ensure that decimals returned from ramsey/uuid
    methods as strings are truly decimals and not some other kind of string.

    To support values as true decimals and not as floats or doubles, we store the decimals as strings.

.. php:class:: Hexadecimal

    Implements :php:interface:`Ramsey\\Uuid\\Type\\TypeInterface`.

    A value object representing a hexadecimal number, for type-safety purposes, to ensure that hexadecimal numbers
    returned from ramsey/uuid methods as strings are truly hexadecimal and not some other kind of string.

.. php:class:: Integer

    Implements :php:interface:`Ramsey\\Uuid\\Type\\NumberInterface`.

    A value object representing an integer, for type-safety purposes, to ensure that integers returned from ramsey/uuid
    methods as strings are truly integers and not some other kind of string.

    To support large integers beyond ``PHP_INT_MAX`` and ``PHP_INT_MIN`` on both 64-bit and 32-bit systems, we store the
    integers as strings.

.. php:class:: Time

    Implements :php:interface:`Ramsey\\Uuid\\Type\\TypeInterface`.

    A value object representing a timestamp, for type-safety purposes, to ensure that timestamps used by ramsey/uuid are
    truly timestamp integers and not some other kind of string or integer.

    .. php:method:: getSeconds()

        :returntype: :php:class:`Ramsey\\Uuid\\Type\\Integer`

    .. php:method:: getMicroseconds()

        :returntype: :php:class:`Ramsey\\Uuid\\Type\\Integer`
