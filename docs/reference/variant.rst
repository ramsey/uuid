.. _reference.variant:

=======
Variant
=======

.. php:namespace:: Ramsey\Uuid

.. php:enum:: Variant : int

    The variant number describes the layout of the UUID. UUIDs generated
    according to the layout defined in `RFC 4122`_ will always have a variant
    value set to ``2``. In ramsey/uuid, this is the enum value
    :php:case:`Ramsey\\Uuid\\Variant::Rfc4122`.

    .. php:case:: ReservedNcs : 0

        Reserved for NCS backward compatibility.

    .. php:case:: Rfc4122 : 2

        The RFC 4122 variant.

    .. php:case:: ReservedMicrosoft : 6

        Reserved for Microsoft Corporation backward compatibility.

    .. php:case:: ReservedFuture : 7

        Reserved for future definition.

.. _RFC 4122: https://www.rfc-editor.org/rfc/rfc4122
