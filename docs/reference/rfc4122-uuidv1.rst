.. _reference.rfc4122.uuidv1:

===============
Rfc4122\\UuidV1
===============

.. php:namespace:: Ramsey\Uuid\Rfc4122

.. php:class:: UuidV1

    Implements :php:interface:`Ramsey\\Uuid\\Rfc4122\\UuidInterface`.

    UuidV1 represents a :ref:`version 1, Gregorian time UUID <rfc4122.version1>`. In addition to providing the methods
    defined on the interface, this class additionally provides the following methods.

    .. php:method:: getDateTime()

        :returns: A date object representing the timestamp associated with the UUID.
        :returntype: ``\DateTimeInterface``
