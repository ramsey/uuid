.. _reference.name-based-namespaces:

=====================
Predefined Namespaces
=====================

`RFC 9562`_ (formerly `RFC 4122`_) defines a handful of UUIDs to use with "for some potentially interesting name spaces."

.. list-table::
    :widths: 30 70
    :align: center
    :header-rows: 1

    * - Constant
      - Description
    * - :php:const:`Uuid::NAMESPACE_DNS <Ramsey\\Uuid\\Uuid::NAMESPACE_DNS>`
      - The name string is a fully-qualified domain name.
    * - :php:const:`Uuid::NAMESPACE_URL <Ramsey\\Uuid\\Uuid::NAMESPACE_URL>`
      - The name string is a URL.
    * - :php:const:`Uuid::NAMESPACE_OID <Ramsey\\Uuid\\Uuid::NAMESPACE_OID>`
      - The name string is an `ISO object identifier (OID)`_.
    * - :php:const:`Uuid::NAMESPACE_X500 <Ramsey\\Uuid\\Uuid::NAMESPACE_X500>`
      - The name string is an `X.500`_ `DN`_ in `DER`_ or a text output format.


.. _RFC 4122: https://tools.ietf.org/html/rfc4122
.. _RFC 9562: https://www.rfc-editor.org/rfc/rfc9562
.. _ISO object identifier (OID): http://www.oid-info.com
.. _X.500: https://en.wikipedia.org/wiki/X.500
.. _DN: https://en.wikipedia.org/wiki/Distinguished_Name
.. _DER: https://www.itu.int/rec/T-REC-X.690/
