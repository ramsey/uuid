.. _reference.rfc4122.uuidv2:

===============
Rfc4122\\UuidV2
===============

.. php:namespace:: Ramsey\Uuid\Rfc4122

.. php:class:: UuidV2

    Implements :php:interface:`Ramsey\\Uuid\\Rfc4122\\UuidInterface`.

    UuidV2 represents a :ref:`version 2, DCE Security UUID <rfc4122.version2>`. In addition to providing the methods
    defined on the interface, this class additionally provides the following methods.

    .. php:method:: getDateTime()

        Returns a `DateTimeInterface <https://www.php.net/datetimeinterface>`_ instance representing the timestamp
        associated with the UUID

        .. caution::

            It is important to note that version 2 UUIDs suffer from some loss of timestamp precision. See
            :ref:`rfc4122.version2.timestamp-problems` to learn more.

        :returns: A date object representing the timestamp associated with the UUID
        :returntype: ``\DateTimeInterface``

    .. php:method:: getLocalDomain()

        :returns: The local domain identifier for this UUID, which is one of :php:const:`Ramsey\\Uuid\\Uuid::DCE_DOMAIN_PERSON`,
                  :php:const:`Ramsey\\Uuid\\Uuid::DCE_DOMAIN_GROUP`, or :php:const:`Ramsey\\Uuid\\Uuid::DCE_DOMAIN_ORG`
        :returntype: ``int``

    .. php:method:: getLocalDomainName()

        :returns: A string name associated with the local domain identifier (one of "person," "group," or "org")
        :returntype: ``string``

    .. php:method:: getLocalIdentifier()

        :returns: The local identifier used when creating this UUID
        :returntype: Ramsey\\Uuid\\Type\\Integer
