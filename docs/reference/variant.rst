.. _reference.variant:

=======
Variant
=======

.. php:namespace:: Ramsey\Uuid

.. php:enum:: Variant : int

    The variant number describes the layout of the UUID. UUIDs generated according to the layout defined in `RFC 9562`_
    (formerly `RFC 4122`_) will always have a variant value set to ``2``. In ramsey/uuid, this is the enum value
    :php:case:`Ramsey\\Uuid\\Variant::Rfc9562`.

    .. php:case:: ReservedNcs : 0

        Reserved for NCS backward compatibility.

    .. php:case:: Rfc9562 : 2

        The `RFC 9562`_ (formerly `RFC 4122`_) variant.

    .. php:case:: ReservedMicrosoft : 6

        Reserved for Microsoft Corporation backward compatibility.

    .. php:case:: ReservedFuture : 7

        Reserved for future definition.

    .. php:const:: Rfc4122

        This is an alias for :php:case:`Ramsey\\Uuid\\Variant::Rfc9562`.

.. _RFC 4122: https://www.rfc-editor.org/rfc/rfc4122
.. _RFC 9562: https://www.rfc-editor.org/rfc/rfc9562
