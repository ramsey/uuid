.. _reference.rfc4122.fieldsinterface:

========================
Rfc4122\\FieldsInterface
========================

.. php:namespace:: Ramsey\Uuid\Rfc4122

.. php:interface:: FieldsInterface

    Implements :php:interface:`Ramsey\\Uuid\\Fields\\FieldsInterface`.

    Rfc4122\\FieldsInterface represents the fields of an `RFC 9562`_ (formerly `RFC 4122`_) UUID. In addition to the
    methods defined on the interface, this class additionally defines the following methods.

    .. php:method:: getClockSeq()

        :returns: The full 16-bit clock sequence, with the variant bits (two most significant bits) masked out.
        :returntype: Ramsey\\Uuid\\Type\\Hexadecimal

    .. php:method:: getClockSeqHiAndReserved()

        :returns: The high field of the clock sequence multiplexed with the variant.
        :returntype: Ramsey\\Uuid\\Type\\Hexadecimal

    .. php:method:: getClockSeqLow()

        :returns: The low field of the clock sequence.
        :returntype: Ramsey\\Uuid\\Type\\Hexadecimal

    .. php:method:: getNode()

        :returns: The node field.
        :returntype: Ramsey\\Uuid\\Type\\Hexadecimal

    .. php:method:: getTimeHiAndVersion()

        :returns: The high field of the timestamp multiplexed with the version.
        :returntype: Ramsey\\Uuid\\Type\\Hexadecimal

    .. php:method:: getTimeLow()

        :returns: The low field of the timestamp.
        :returntype: Ramsey\\Uuid\\Type\\Hexadecimal

    .. php:method:: getTimeMid()

        :returns: The middle field of the timestamp.
        :returntype: Ramsey\\Uuid\\Type\\Hexadecimal

    .. php:method:: getTimestamp()

        :returns: The full 60-bit timestamp, without the version.
        :returntype: Ramsey\\Uuid\\Type\\Hexadecimal

    .. php:method:: getVariant()

        Returns the variant, which, for `RFC 9562`_ (formerly `RFC 4122`_) variant UUIDs, should always be the value ``2``.

        :returns: The UUID variant.
        :returntype: ``int``

    .. php:method:: getVersion()

        :returns: The UUID version.
        :returntype: ``int``

    .. php:method:: isNil()

        A *nil* UUID is a special type of UUID with all 128 bits set to zero. Its string standard representation is
        always ``00000000-0000-0000-0000-000000000000``.

        :returns: True if this UUID represents a nil UUID.
        :returntype: ``bool``

.. _RFC 4122: https://www.rfc-editor.org/rfc/rfc4122
.. _RFC 9562: https://www.rfc-editor.org/rfc/rfc9562
