.. _reference.rfc4122.uuidv7:

===============
Rfc4122\\UuidV7
===============

.. php:namespace:: Ramsey\Uuid\Rfc4122

.. php:class:: UuidV7

    Implements :php:interface:`Ramsey\\Uuid\\Rfc4122\\UuidInterface`.

    UuidV7 represents a :ref:`version 7, Unix Epoch time UUID <rfc4122.version7>`. In addition to providing the methods
    defined on the interface, this class additionally provides the following methods.

    .. php:method:: getDateTime()

        :returns: A date object representing the timestamp associated with the UUID.
        :returntype: ``\DateTimeInterface``
