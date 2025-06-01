.. _introduction:

============
Introduction
============

ramsey/uuid is a PHP library for generating and working with `RFC 9562`_ (formerly `RFC 4122`_) version 1, 2, 3, 4, 5,
6, 7, and 8 universally unique identifiers (UUID). ramsey/uuid also supports optional and non-standard features, such as
GUIDs and other approaches for encoding/decoding UUIDs.

What Is a UUID?
###############

A universally unique identifier, or UUID, is a 128-bit unsigned integer, usually represented as a hexadecimal string
split into five groups with dashes. The most widely-known and used types of UUIDs are defined by `RFC 9562`_ (formerly
`RFC 4122`_).

A UUID, when encoded in hexadecimal string format, looks like:

.. code-block:: text

    ebb5c735-0308-4e3c-9aea-8a270aebfe15

The probability of duplicating a UUID is close to zero, so they are a great choice for generating unique identifiers in
distributed systems.

UUIDs can also be stored in binary format, as a string of 16 bytes.

.. _RFC 4122: https://www.rfc-editor.org/rfc/rfc4122
.. _RFC 9562: https://www.rfc-editor.org/rfc/rfc9562
