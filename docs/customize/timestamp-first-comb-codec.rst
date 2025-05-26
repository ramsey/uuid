.. _customize.timestamp-first-comb-codec:

==========================
Timestamp-first COMB Codec
==========================

.. attention::

    :ref:`Version 7, Unix Epoch time UUIDs <rfc4122.version7>` are a new version
    of UUID that eliminate the need for the timestamp-first COMB codec. If you
    aren't currently using the timestamp-first COMB codec, and you need
    time-based, sortable UUIDs, consider using version 7 UUIDs.

:ref:`Version 4, random UUIDs <rfc4122.version4>` are doubly problematic when it
comes to sorting and storing to databases (see :ref:`database.order`), since
their values are random, and there is no timestamp associated with them that may
be rearranged, like with the :ref:`ordered-time codec
<customize.ordered-time-codec>`. In 2002, Jimmy Nilsson recognized this problem
with random UUIDs and proposed a solution he called "COMBs" (see "`The Cost of
GUIDs as Primary Keys`_").

So-called because they *combine* random bytes with a timestamp, the
timestamp-first COMB codec replaces the first 48 bits of a version 4, random
UUID with a Unix timestamp and microseconds, creating an identifier that can be
sorted by creation time. These UUIDs are *monotonically increasing*, each one
coming after the previously-created one, in a proper sort order.

.. code-block:: php
    :caption: Use the timestamp-first COMB codec to generate a version 4 UUID
    :name: customize.timestamp-first-comb-codec-example

    use Ramsey\Uuid\Codec\TimestampFirstCombCodec;
    use Ramsey\Uuid\Generator\CombGenerator;
    use Ramsey\Uuid\UuidFactory;

    $factory = new UuidFactory();
    $codec = new TimestampFirstCombCodec($factory->getUuidBuilder());

    $factory->setCodec($codec);

    $factory->setRandomGenerator(new CombGenerator(
        $factory->getRandomGenerator(),
        $factory->getNumberConverter()
    ));

    $timestampFirstComb = $factory->uuid4();

    printf(
        "UUID: %s\nVersion: %d\nBytes: %s\n",
        $timestampFirstComb->toString(),
        $timestampFirstComb->getFields()->getVersion(),
        bin2hex($timestampFirstComb->getBytes())
    );

This will use the timestamp-first COMB codec to generate a version 4 UUID with
the timestamp replacing the first 48 bits and will print out details about the
UUID similar to these:

.. code-block:: text

    UUID: 9009ebcc-cd99-4b5f-90cf-9155607d2de9
    Version: 4
    Bytes: 9009ebcccd994b5f90cf9155607d2de9

Note that the bytes are in the same order as the string representation. Unlike
the :ref:`ordered-time codec <customize.ordered-time-codec>`, the
timestamp-first COMB codec affects both the string representation and the byte
representation. This means either the string UUID or the bytes may be stored to
a datastore and sorted. To learn more, see :ref:`database`.


.. _The Cost of GUIDs as Primary Keys: https://www.informit.com/articles/printerfriendly/25862
