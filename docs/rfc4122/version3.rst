.. _rfc4122.version3:

===========================
Version 3: Name-based (MD5)
===========================

The first thing that comes to mind with most people think of a UUID is a
*random* identifier, but name-based UUIDs aren't random at all. In fact, they're
deterministic. For any given identical namespace and name, you will always
generate the same UUID.

Name-based UUIDs are useful when you need an identifier that's based on
something's name and will always be the same for that name.

For example, let's say I want to create an identifier for a URL. I could use
a :ref:`version 1 <rfc4122.version1>` or :ref:`version 4 <rfc4122.version4>`
UUID to create an identifier for the URL, but what if I'm working with a
distributed system, and I want to ensure that every client in this system can
always generate the same identifier for any given URL in the system?

This is where a name-based UUID comes in handy.

Name-based UUIDs combine a namespace with a name. This way, the UUIDs are unique
to the namespace they're created in. RFC 4122 defines some
:ref:`predefined namespaces <reference.name-based-namespaces>`, one of which is
for URLs.

.. code-block:: php
    :caption: Generate a name-based UUID for a URL

    use Ramsey\Uuid\Uuid;

    $uuid = Uuid::uuid3(Uuid::NAMESPACE_URL, 'https://www.php.net');

The UUID generated will always be the same, as long as the namespace and name
are the same.

.. hint::
    The version 3 UUID for "https://www.php.net" in the URL namespace will
    always be ``3f703955-aaba-3e70-a3cb-baff6aa3b28f``. See for yourself. Run
    the code above, and you'll see it always generates the same UUID.


.. _rfc4122.version3.custom-namespaces:

Custom Namespaces
#################

If you're working with name-based UUIDs for names that don't fit into any of
the :ref:`predefined namespaces <reference.name-based-namespaces>`, or you don't
want to use any of the predefined namespaces, you can create your own namespace.

The best way to do this is to generate a :ref:`version 1 <rfc4122.version1>` or
:ref:`version 4 <rfc4122.version4>` UUID and save this UUID as your namespace.

.. code-block:: php
    :caption: Generate a custom namespace UUID

    use Ramsey\Uuid\Uuid;

    $uuid = Uuid::uuid4();

    printf("My namespace UUID is %s\n", $uuid->toString());

This will generate a random UUID, which we'll store to a constant so we can
reuse it as our own custom namespace.

.. code-block:: php
    :caption: Use a custom namespace to create name-based UUIDs

    use Ramsey\Uuid\Uuid;

    const MY_NAMESPACE = '9a494836-ef67-4c63-a27b-15bc5a17e0ed';

    $uuid = Uuid::uuid3(MY_NAMESPACE, 'widget/1234567890');

With this custom namespace, the version 3 UUID for the name "widget/1234567890"
will always be ``f8e9b8cf-43a2-378b-9de0-aa714f6e989b``.
