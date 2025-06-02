.. _reference.guid.guid:

==========
Guid\\Guid
==========

.. php:namespace:: Ramsey\Uuid\Guid

.. php:class:: Guid

    Implements :php:interface:`Ramsey\\Uuid\\UuidInterface`.

    Guid represents a :ref:`nonstandard.guid`. In addition to providing the methods defined on the interface, this class
    additionally provides the following methods.

    .. php:method:: getFields()

        :returns: The fields that comprise this GUID.
        :returntype: Ramsey\\Uuid\\Guid\\Fields
