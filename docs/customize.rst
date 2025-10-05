.. _customize:

=============
Customization
=============

.. toctree::
    :titlesonly:
    :hidden:

    customize/ordered-time-codec
    customize/timestamp-first-comb-codec
    customize/calculators
    customize/validators
    customize/factory

ramsey/uuid offers a variety of ways to modify the standard behavior of the library through dependency injection. Using
`FeatureSet`_, `UuidFactory`_, and :php:meth:`Uuid::setFactory() <Ramsey\\Uuid\\Uuid::setFactory()>`, you are able to
replace just about any `builder`_, `codec`_, `converter`_, `generator`_, `provider`_, and more.

Ordered-time Codec *(deprecated)*
    The ordered-time codec exists to rearrange the bytes of a version 1, Gregorian time UUID so that the timestamp
    portion of the UUID is monotonically increasing. To learn more, see :ref:`customize.ordered-time-codec`.

Timestamp-first COMB Codec *(deprecated)*
    The timestamp-first COMB codec replaces part of a version 4, random UUID with a timestamp, so that the UUID becomes
    monotonically increasing. To learn more, see :ref:`customize.timestamp-first-comb-codec`.

Using a Custom Calculator
    It's possible to replace the default calculator ramsey/uuid uses. If your requirements require a different solution
    for making calculations, see :ref:`customize.calculators`.

Using a Custom Validator
    If your requirements require a different level of validation or a different UUID format, you may replace the default
    validator. See :ref:`customize.validators`, to learn more.

Replace the Default Factory
    Not only are you able to inject alternate builders, codecs, etc. into the factory and use the factory to generate
    UUIDs, you may also replace the global, static factory used by the static methods on the Uuid class. To find out
    how, see :ref:`customize.factory`.

.. _UuidFactory: https://github.com/ramsey/uuid/blob/4.x/src/UuidFactory.php
.. _FeatureSet: https://github.com/ramsey/uuid/blob/4.x/src/FeatureSet.php
.. _codec: https://github.com/ramsey/uuid/tree/4.x/src/Codec
.. _builder: https://github.com/ramsey/uuid/tree/4.x/src/Builder
.. _converter: https://github.com/ramsey/uuid/tree/4.x/src/Converter
.. _provider: https://github.com/ramsey/uuid/tree/4.x/src/Provider
.. _generator: https://github.com/ramsey/uuid/tree/4.x/src/Generator
