.. _rfc4122.version4:

=================
Version 4: Random
=================

Version 4 UUIDs are perhaps the most popular form of UUID. They are randomly-generated and do not contain any
information about the time they are created or the machine that generated them. If you don't care about this information,
then a version 4 UUID might be perfect for your needs.

.. code-block:: php
    :caption: Generate a version 4, random UUID
    :name: rfc4122.version4.example

    use Ramsey\Uuid\Uuid;

    $uuid = Uuid::uuid4();

    printf(
        "UUID: %s\nVersion: %d\n",
        $uuid->toString(),
        $uuid->getFields()->getVersion()
    );

This will generate a version 4 UUID and print out its string representation. It will look something like this:

.. code-block:: text

    UUID: 1ee9aa1b-6510-4105-92b9-7171bb2f3089
    Version: 4

.. tip::

    Version 4 UUIDs generated in ramsey/uuid are instances of UuidV4. Check out the
    :php:class:`Ramsey\\Uuid\\Rfc4122\\UuidV4` API documentation to learn more about what you can do with a UuidV4
    instance.
