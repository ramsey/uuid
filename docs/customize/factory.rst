.. _customize.factory:

===========================
Replace the Default Factory
===========================

In many of the examples throughout this documentation, we've seen how to configure the factory and then use that factory
to generate and work with UUIDs.

For example:

.. code-block:: php
    :caption: Configure the factory and use it to generate a version 1 UUID
    :name: customize.factory.example

    use Ramsey\Uuid\Codec\OrderedTimeCodec;
    use Ramsey\Uuid\UuidFactory;

    $factory = new UuidFactory();
    $codec = new OrderedTimeCodec($factory->getUuidBuilder());

    $factory->setCodec($codec);

    $orderedTimeUuid = $factory->uuid1();

When doing this, the default behavior of ramsey/uuid is left intact. If we call ``Uuid::uuid1()`` to generate a version
1 UUID after configuring the factory as shown above, it won't use :ref:`OrderedTimeCodec <customize.ordered-time-codec>`
to generate the UUID.

.. code-block:: php
    :caption: The behavior differs between $factory->uuid1() and Uuid::uuid1()
    :name: customize.factory.behavior-example

    $orderedTimeUuid = $factory->uuid1();

    printf(
        "UUID: %s\nBytes: %s\n\n",
        $orderedTimeUuid->toString(),
        bin2hex($orderedTimeUuid->getBytes())
    );

    $uuid = Uuid::uuid1();

    printf(
        "UUID: %s\nBytes: %s\n\n",
        $uuid->toString(),
        bin2hex($uuid->getBytes())
    );

In this example, we print out details for two different UUIDs. The first was generated with the :ref:`OrderedTimeCodec
<customize.ordered-time-codec>` using ``$factory->uuid1()``. The second was generated using ``Uuid::uuid1()``. It looks
something like this:

.. code-block:: text

    UUID: 2ff06620-6251-11ea-9791-0242ac130003
    Bytes: 11ea62512ff0662097910242ac130003

    UUID: 2ff09730-6251-11ea-ba64-0242ac130003
    Bytes: 2ff09730625111eaba640242ac130003

Notice the arrangement of the bytes. The first set of bytes has been rearranged, according to the ordered-time codec
rules, but the second set of bytes remains in the same order as the UUID string.

*Configuring the factory does not change the default behavior.*

If we want to change the default behavior, we must *replace* the factory used by the Uuid static methods, and we can do
this using the :php:meth:`Uuid::setFactory() <Ramsey\\Uuid\\Uuid::setFactory>` static method.

.. code-block:: php
    :caption: Replace the factory to globally affect Uuid behavior
    :name: customize.factory.replace-factory-example

    Uuid::setFactory($factory);

    $uuid = Uuid::uuid1();

Now, every time we call :php:meth:`Uuid::uuid() <Ramsey\\Uuid\\Uuid::uuid1>`, ramsey/uuid will use the factory configured
with the :ref:`OrderedTimeCodec <customize.ordered-time-codec>` to generate version 1 UUIDs.

.. warning::

    Calling :php:meth:`Uuid::setFactory() <Ramsey\\Uuid\\Uuid::setFactory>` to replace the factory will change the
    behavior of Uuid no matter where it is used, so keep this in mind when replacing the factory. If you replace the
    factory deep inside a method somewhere, any later code that calls a static method on :php:class:`Ramsey\\Uuid\\Uuid`
    will use the new factory to generate UUIDs.
