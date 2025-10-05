.. _nonstandard.other:

=======================
Other Nonstandard UUIDs
=======================

Sometimes, you might encounter a string that looks like a UUID but doesn't follow the `RFC 9562`_ (formerly `RFC 4122`_)
specification. Take this string, for example:

.. code-block:: text

    d95959bc-2ff5-43eb-fccd-14883ba8f174

At a glance, this looks like a valid UUID, but the variant bits don't match `RFC 9562`_ (formerly `RFC 4122`_). Instead
of throwing a validation exception, ramsey/uuid will assume this is a UUID, since it fits the format and has 128 bits,
but it will represent it as a :php:class:`Ramsey\\Uuid\\Nonstandard\\Uuid`.

.. code-block:: php
    :caption: Create an instance of :php:class:`Ramsey\\Uuid\\Nonstandard\\Uuid` from a non-RFC 9562 UUID

    use Ramsey\Uuid\Uuid;

    $uuid = Uuid::fromString('d95959bc-2ff5-43eb-fccd-14883ba8f174');

    printf(
        "Class: %s\nUUID: %s\nVersion: %d\nVariant: %s\n",
        get_class($uuid),
        $uuid->toString(),
        $uuid->getFields()->getVersion(),
        $uuid->getFields()->getVariant()
    );

This will create a :php:class:`Ramsey\\Uuid\\Nonstandard\\Uuid` from the given string and print out a few details about
it. It will look something like this:

.. code-block:: text

    Class: Ramsey\Uuid\Nonstandard\Uuid
    UUID: d95959bc-2ff5-43eb-fccd-14883ba8f174
    Version: 0
    Variant: 7

Note that the version is 0. Since the variant is 7, and there is no formal specification for this variant of UUID,
ramsey/uuid has no way of knowing what type of UUID this is.

.. _RFC 4122: https://www.rfc-editor.org/rfc/rfc4122
.. _RFC 9562: https://www.rfc-editor.org/rfc/rfc9562
