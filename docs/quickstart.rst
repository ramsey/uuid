.. _quickstart:

===============
Getting Started
===============

Requirements
############

ramsey/uuid |version| requires the following:

* PHP 8.0+
* `ext-json <https://www.php.net/manual/en/book.json.php>`_

The JSON extension is normally enabled by default, but it is possible to disable it. Other required extensions include
`PCRE <https://www.php.net/manual/en/book.pcre.php>`_ and `SPL <https://www.php.net/manual/en/book.spl.php>`_. These
standard extensions cannot be disabled without patching PHP's build system and/or C sources.

ramsey/uuid recommends installing/enabling the following extensions. While not required, these extensions improve the
performance of ramsey/uuid.

* `ext-gmp <https://www.php.net/manual/en/book.gmp.php>`_
* `ext-bcmath <https://www.php.net/manual/en/book.bc.php>`_

Install With Composer
#####################

The only supported installation method for ramsey/uuid is `Composer <https://getcomposer.org>`_. Use the following
command to add ramsey/uuid to your project dependencies:

.. code-block:: bash

    composer require ramsey/uuid

Using ramsey/uuid
#################

After installing ramsey/uuid, the quickest way to get up-and-running is to use the static generation methods.

.. code-block:: php

    use Ramsey\Uuid\Uuid;

    $uuid = Uuid::uuid4();

    printf(
        "UUID: %s\nVersion: %d\n",
        $uuid->toString(),
        $uuid->getFields()->getVersion()
    );

This will return an instance of :php:class:`Ramsey\\Uuid\\Rfc4122\\UuidV4`.

.. tip::
    .. rubric:: Use the Interfaces

    Feel free to use ``instanceof`` to check the specific instance types of UUIDs. However, when using type hints, it's
    best to use the interfaces.

    The most lenient interface is :php:interface:`Ramsey\\Uuid\\UuidInterface`, while
    :php:interface:`Ramsey\\Uuid\\Rfc4122\\UuidInterface` ensures the UUIDs you're using conform to the `RFC 9562`_
    (formerly `RFC 4122`_) standard. If you're not sure which one to use, start with the stricter
    :php:interface:`Rfc4122\\UuidInterface <Ramsey\\Uuid\\Rfc4122\\UuidInterface>`.

ramsey/uuid provides a number of helpful static methods that help you work with and generate most types of UUIDs,
without any special customization of the library.

.. list-table::
    :widths: 25 75
    :align: center
    :header-rows: 1

    * - Method
      - Description
    * - :php:meth:`Uuid::uuid1() <Ramsey\\Uuid\\Uuid::uuid1>`
      - This generates a :ref:`rfc4122.version1` UUID.
    * - :php:meth:`Uuid::uuid2() <Ramsey\\Uuid\\Uuid::uuid2>`
      - This generates a :ref:`rfc4122.version2` UUID.
    * - :php:meth:`Uuid::uuid3() <Ramsey\\Uuid\\Uuid::uuid3>`
      - This generates a :ref:`rfc4122.version3` UUID.
    * - :php:meth:`Uuid::uuid4() <Ramsey\\Uuid\\Uuid::uuid4>`
      - This generates a :ref:`rfc4122.version4` UUID.
    * - :php:meth:`Uuid::uuid5() <Ramsey\\Uuid\\Uuid::uuid5>`
      - This generates a :ref:`rfc4122.version5` UUID.
    * - :php:meth:`Uuid::uuid6() <Ramsey\\Uuid\\Uuid::uuid6>`
      - This generates a :ref:`rfc4122.version6` UUID.
    * - :php:meth:`Uuid::uuid7() <Ramsey\\Uuid\\Uuid::uuid7>`
      - This generates a :ref:`rfc4122.version7` UUID.
    * - :php:meth:`Uuid::uuid8() <Ramsey\\Uuid\\Uuid::uuid8>`
      - This generates a :ref:`rfc4122.version8` UUID.
    * - :php:meth:`Uuid::isValid() <Ramsey\\Uuid\\Uuid::isValid>`
      - Checks whether a string is a valid UUID.
    * - :php:meth:`Uuid::fromString() <Ramsey\\Uuid\\Uuid::fromString>`
      - Creates a UUID instance from a string UUID.
    * - :php:meth:`Uuid::fromBytes() <Ramsey\\Uuid\\Uuid::fromBytes>`
      - Creates a UUID instance from a 16-byte string.
    * - :php:meth:`Uuid::fromInteger() <Ramsey\\Uuid\\Uuid::fromInteger>`
      - Creates a UUID instance from a string integer.
    * - :php:meth:`Uuid::fromDateTime() <Ramsey\\Uuid\\Uuid::fromDateTime>`
      - Creates a version 1 UUID instance from a PHP `DateTimeInterface`_.

.. _RFC 4122: https://www.rfc-editor.org/rfc/rfc4122
.. _RFC 9562: https://www.rfc-editor.org/rfc/rfc9562
.. _DateTimeInterface: https://www.php.net/datetimeinterface
