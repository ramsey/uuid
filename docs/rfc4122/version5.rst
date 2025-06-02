.. _rfc4122.version5:

=============================
Version 5: Name-based (SHA-1)
=============================

.. danger::

    Since :ref:`version 3 <rfc4122.version3>` and version 5 UUIDs essentially use a *salt* (the namespace) to hash data,
    it may be tempting to use them to hash passwords. **DO NOT do this under any circumstances!** You should not store
    any sensitive information in a version 3 or version 5 UUID, since `MD5 and SHA-1 are insecure and have known attacks
    demonstrated against them <https://en.wikipedia.org/wiki/Hash_function_security_summary>`_.

    *Use these types of UUIDs as identifiers only.*

The first thing that comes to mind with most people think of a UUID is a *random* identifier, but name-based UUIDs
aren't random at all. In fact, they're deterministic. For any given identical namespace and name, you will always
generate the same UUID.

Name-based UUIDs are useful when you need an identifier that's based on something's *name* --- think *identity* --- and
will always be the same no matter where or when it is created.

For example, let's say I want to create an identifier for a URL. I could use a :ref:`version 1 <rfc4122.version1>` or
:ref:`version 4 <rfc4122.version4>` UUID to create an identifier for the URL, but what if I'm working with a distributed
system, and I want to ensure that every client in this system can always generate the same identifier for any given URL?

This is where a name-based UUID comes in handy.

Name-based UUIDs combine a namespace with a name. This way, the UUIDs are unique to the namespace they're created in.
`RFC 9562`_ defines some :ref:`predefined namespaces <reference.name-based-namespaces>`, one of which is for URLs.

.. note::

    Version 5 UUIDs use `SHA-1`_ as the hashing algorithm for combining the namespace and the name.

.. code-block:: php
    :caption: Generate a version 5, name-based UUID for a URL
    :name: rfc4122.version5.url-example

    use Ramsey\Uuid\Uuid;

    $uuid = Uuid::uuid5(Uuid::NAMESPACE_URL, 'https://www.php.net');

The UUID generated will always be the same, as long as the namespace and name are the same. The version 5 UUID for
"https://www.php.net" in the URL namespace will always be ``a8f6ae40-d8a7-58f0-be05-a22f94eca9ec``. See for yourself.
Run the code above, and you'll see it always generates the same UUID.

.. tip::

    Version 5 UUIDs generated in ramsey/uuid are instances of UuidV5. Check out the
    :php:class:`Ramsey\\Uuid\\Rfc4122\\UuidV5` API documentation to learn more about what you can do with a UuidV5
    instance.

.. _rfc4122.version5.custom-namespaces:

Custom Namespaces
#################

If you're working with name-based UUIDs for names that don't fit into any of the :ref:`predefined namespaces
<reference.name-based-namespaces>`, or you don't want to use any of the predefined namespaces, you can create your own
namespace.

The best way to do this is to generate a :ref:`version 1 <rfc4122.version1>` or :ref:`version 4 <rfc4122.version4>` UUID
and save this UUID as your namespace.

.. code-block:: php
    :caption: Generate a custom namespace UUID
    :name: rfc4122.version5.create-namespace

    use Ramsey\Uuid\Uuid;

    $uuid = Uuid::uuid1();

    printf("My namespace UUID is %s\n", $uuid->toString());

This will generate a version 1, Gregorian time UUID, which we'll store to a constant so we can reuse it as our own
custom namespace.

.. code-block:: php
    :caption: Use a custom namespace to create version 5, name-based UUIDs
    :name: rfc4122.version5.custom-example

    use Ramsey\Uuid\Uuid;

    const WIDGET_NAMESPACE = '4bdbe8ec-5cb5-11ea-bc55-0242ac130003';

    $uuid = Uuid::uuid5(WIDGET_NAMESPACE, 'widget/1234567890');

With this custom namespace, the version 5 UUID for the name "widget/1234567890" will always be
``a35477ae-bfb1-5f2e-b5a4-4711594d855f``.

We can publish this namespace, allowing others to use it to generate identifiers for widgets. When two or more systems
try to reference the same widget, they'll end up generating the same identifier for it, which is exactly what we want.

.. _RFC 9562: https://www.rfc-editor.org/rfc/rfc9562
.. _SHA-1: https://en.wikipedia.org/wiki/SHA-1
