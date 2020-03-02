.. _reference.rfc4122.fieldsinterface:

========================
Rfc4122\\FieldsInterface
========================

All RFC 4122 UUID instances in ramsey/uuid implement a ``getFields()`` method
that returns an instance of ``Rfc4122\FieldsInterface``.

.. php:namespace:: Ramsey\Uuid\Rfc4122

.. php:interface:: FieldsInterface

    Represents the fields of an RFC 4122 UUID.

    .. php:method:: getBytes()

        :returns: (*string*) The bytes that comprise these fields.

    .. php:method:: getClockSeq()

        :returns: (*Ramsey\\Uuid\\Type\\Hexadecimal*) The full 16-bit clock sequence, with the variant bits (two most significant bits) masked out.

    .. php:method:: getClockSeqHiAndReserved()

        :returns: (*Ramsey\\Uuid\\Type\\Hexadecimal*) The high field of the clock sequence multiplexed with the variant.

    .. php:method:: getClockSeqLow()

        :returns: (*Ramsey\\Uuid\\Type\\Hexadecimal*) The low field of the clock sequence.

    .. php:method:: getNode()

        :returns: (*Ramsey\\Uuid\\Type\\Hexadecimal*) The node field.

    .. php:method:: getTimeHiAndVersion()

        :returns: (*Ramsey\\Uuid\\Type\\Hexadecimal*) The high field of the timestamp multiplexed with the version.

    .. php:method:: getTimeLow()

        :returns: (*Ramsey\\Uuid\\Type\\Hexadecimal*) The low field of the timestamp.

    .. php:method:: getTimeMid()

        :returns: (*Ramsey\\Uuid\\Type\\Hexadecimal*) The middle field of the timestamp.

    .. php:method:: getTimestamp()

        :returns: (*Ramsey\\Uuid\\Type\\Hexadecimal*) The full 60-bit timestamp, without the version.

    .. php:method:: getVariant()

        Returns the variant, which, for RFC 4122 variant UUIDs, should always be
        the value ``2``.

        :returns: (*int*) The UUID variant.

    .. php:method:: getVersion()

        :returns: (*int*) The UUID version.

    .. php:method:: isNil()

        A *nil* UUID is a special type of UUID with all 128 bits set to zero.
        Its string standard representation is always
        ``00000000-0000-0000-0000-000000000000``.

        :returns: (*bool*) True if this UUID represents a nil UUID.
