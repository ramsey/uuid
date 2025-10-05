.. _rfc4122.version3:

===========================
Version 3: Name-based (MD5)
===========================

.. attention::

    `RFC 9562`_ states, "Where possible, UUIDv5 SHOULD be used in lieu of UUIDv3." Unless you have a specific use-case
    for version 3 UUIDs, use :ref:`version 5 UUIDs <rfc4122.version5>`.

.. note::

    To learn about name-based UUIDs, read the section :ref:`rfc4122.version5`. Version 3 UUIDs behave exactly the same
    as :ref:`version 5 UUIDs <rfc4122.version5>`. The only difference is the hashing algorithm used to generate the UUID.

    Version 3 UUIDs use `MD5`_ as the hashing algorithm for combining the namespace and the name.

Due to the use of a different hashing algorithm, version 3 UUIDs generated with any given namespace and name will differ
from version 5 UUIDs generated using the same namespace and name.

As an example, let's take a look at generating a version 3 UUID using the same namespace and name used in
":ref:`rfc4122.version5.url-example`."

.. code-block:: php
    :caption: Generate a version 3, name-based UUID for a URL
    :name: rfc4122.version3.url-example

    use Ramsey\Uuid\Uuid;

    $uuid = Uuid::uuid3(Uuid::NAMESPACE_URL, 'https://www.php.net');

Even though the namespace and name are the same, the version 3 UUID generated will always be
``3f703955-aaba-3e70-a3cb-baff6aa3b28f``.

Likewise, we can use the custom namespace we created in ":ref:`rfc4122.version5.create-namespace`" to generate a version
3 UUID, but the result will be different from the version 5 UUID with the same custom namespace and name.

.. code-block:: php
    :caption: Use a custom namespace to create version 3, name-based UUIDs
    :name: rfc4122.version3.custom-example

    use Ramsey\Uuid\Uuid;

    const WIDGET_NAMESPACE = '4bdbe8ec-5cb5-11ea-bc55-0242ac130003';

    $uuid = Uuid::uuid3(WIDGET_NAMESPACE, 'widget/1234567890');

With this custom namespace, the version 3 UUID for the name "widget/1234567890" will always be
``53564aa3-4154-3ca5-ac90-dba59dc7d3cb``.

.. tip::

    Version 3 UUIDs generated in ramsey/uuid are instances of UuidV3. Check out the
    :php:class:`Ramsey\\Uuid\\Rfc4122\\UuidV3` API documentation to learn more about what you can do with a UuidV3
    instance.

.. _RFC 9562: https://www.rfc-editor.org/rfc/rfc9562
.. _MD5: https://en.wikipedia.org/wiki/MD5
