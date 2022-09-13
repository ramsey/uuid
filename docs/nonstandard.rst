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

Outside of `RFC 4122`_, other types of UUIDs are in-use, following rules of
their own. Some of these are on their way to becoming accepted standards, while
others have historical reasons for remaining valid today. Still, others are
completely random and do not follow any rules.

For these cases, ramsey/uuid provides a special functionality to handle these
alternate, nonstandard forms.

Version 6: Reordered Time
    This is a new version of UUID that combines the features of a
    :ref:`version 1 UUID <rfc4122.version1>` with a *monotonically increasing*
    UUID. For more details, see :ref:`nonstandard.version6`.

Globally Unique Identifiers (GUIDs)
    A globally unique identifier, or GUID, is often used as a synonym for UUID.
    A key difference is the order of the bytes. Any `RFC 4122`_ version UUID may
    be represented as a GUID. For more details, see :ref:`nonstandard.guid`.

Other Nonstandard UUIDs
    Sometimes, UUID string or byte representations don't follow `RFC 4122`_.
    Rather than reject these identifiers, ramsey/uuid returns them with the
    special Nonstandard\\Uuid instance type. For more details, see
    :ref:`nonstandard.other`.


.. _RFC 4122: https://tools.ietf.org/html/rfc4122
