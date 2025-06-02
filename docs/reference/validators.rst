.. _reference.validators:

==========
Validators
==========

.. php:namespace:: Ramsey\Uuid\Validator

.. php:interface:: ValidatorInterface

    .. php:method:: getPattern()

        :returns: The regular expression pattern used by this validator
        :returntype: ``string``

    .. php:method:: validate($uuid)

        :param string $uuid: The string to validate as a UUID
        :returns: True if the provided string represents a UUID, false otherwise
        :returntype: ``bool``

.. php:class:: GenericValidator

    Implements :php:interface:`Ramsey\\Uuid\\Validator\\ValidatorInterface`.

    GenericValidator validates strings as UUIDs of any variant.

.. php:namespace:: Ramsey\Uuid\Rfc4122

.. php:class:: Validator

    Implements :php:interface:`Ramsey\\Uuid\\Validator\\ValidatorInterface`.

    Rfc4122\\Validator validates strings as UUIDs of the `RFC 9562`_ (formerly `RFC 4122`_) variant.

.. _RFC 4122: https://www.rfc-editor.org/rfc/rfc4122
.. _RFC 9562: https://www.rfc-editor.org/rfc/rfc9562
