.. _nonstandard:

=================
Nonstandard UUIDs
=================

.. toctree::
    :titlesonly:
    :hidden:

    nonstandard/version6
    nonstandard/guid
    nonstandard/other

Outside of `RFC 9562`_ (formerly `RFC 4122`_), other types of UUIDs are in-use, following rules of their own. Some of
these are on their way to becoming accepted standards, while others have historical reasons for remaining valid today.
Still, others are completely random and do not follow any rules.

For these cases, ramsey/uuid provides a special functionality to handle these alternate, nonstandard forms.

Globally Unique Identifiers (GUIDs)
    A globally unique identifier, or GUID, is often used as a synonym for UUID. A key difference is the order of the
    bytes. Any `RFC 9562`_ version UUID may be represented as a GUID. For more details, see :ref:`nonstandard.guid`.

Other Nonstandard UUIDs
    Sometimes, UUID string or byte representations don't follow `RFC 9562`_. Rather than reject these identifiers,
    ramsey/uuid returns them with the special Nonstandard\\Uuid instance type. For more details, see
    :ref:`nonstandard.other`.

.. _RFC 4122: https://www.rfc-editor.org/rfc/rfc4122
.. _RFC 9562: https://www.rfc-editor.org/rfc/rfc9562
