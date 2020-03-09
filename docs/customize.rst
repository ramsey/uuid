.. _customize:

=============
Customization
=============

.. toctree::
    :titlesonly:
    :hidden:

    customize/ordered-time-codec
    customize/timestamp-first-comb-codec

ramsey/uuid offers a variety of ways to modify the standard behavior of the
library through dependency injection. Using `UuidFactory`_, `FeatureSet`_, and
:php:meth:`Uuid::setFactory() <Ramsey\\Uuid\\Uuid::setFactory()>`, you are able
to replace just about any `codec`_, `builder`_, `converter`_, `provider`_,
`generator`_, and more.

Ordered-time Codec
    The ordered-time codec exists to rearrange the bytes of a version 1,
    time-based UUID so that the timestamp portion of the UUID is monotonically
    increasing. To learn more, see :ref:`customize.ordered-time-codec`.

Timestamp-first COMB Codec
    The timestamp-first COMB codec replaces part of a version 4, random UUID
    with a timestamp, so that the UUID becomes monotonically increasing. To
    learn more, see :ref:`customize.timestamp-first-comb-codec`.


.. _UuidFactory: https://github.com/ramsey/uuid/blob/master/src/UuidFactory.php
.. _FeatureSet: https://github.com/ramsey/uuid/blob/master/src/FeatureSet.php
.. _codec: https://github.com/ramsey/uuid/tree/master/src/Codec
.. _builder: https://github.com/ramsey/uuid/tree/master/src/Builder
.. _converter: https://github.com/ramsey/uuid/tree/master/src/Converter
.. _provider: https://github.com/ramsey/uuid/tree/master/src/Provider
.. _generator: https://github.com/ramsey/uuid/tree/master/src/Generator
