.. _rfc4122.version4:

=================
Version 4: Random
=================

Version 4 UUIDs are perhaps the most popular form of UUID. They are
randomly-generated and do not contain any information about the time they are
created or the machine that generated them. If you don't care about this
information, then a version 4 UUID might be perfect for your needs.

To generate a version 4 UUID, you may use the ``Uuid::uuid4()`` static method.

.. code-block:: php

    use Ramsey\Uuid\Uuid;

    $uuid = Uuid::uuid4();

    printf(
        "UUID: %s\nVersion: %d\n",
        $uuid->toString(),
        $uuid->getFields()->getVersion()
    );

After creating a ``UuidInterface`` object from a string (or bytes), you can
check to see if it's a version 4 UUID by checking its instance type.

.. code-block:: php

    use Ramsey\Uuid\Rfc4122\UuidV4;
    use Ramsey\Uuid\Uuid;

    $uuid = Uuid::fromString('6b8d3b65-a527-49d5-b6dc-cf195877feef');

    if ($uuid instanceof UuidV4) {
        printf("%s is a version 4 UUID!\n", $uuid->toString());
    }

.. tip::
    Check out the :php:interface:`Ramsey\\Uuid\\Rfc4122\\UuidInterface` API
    documentation to learn more about what you can do with a ``UuidV4``
    instance.
