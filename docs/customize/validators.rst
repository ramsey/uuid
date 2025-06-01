.. _customize.validators:

========================
Using a Custom Validator
========================

By default, ramsey/uuid validates UUID strings with the lenient validator :php:class:`Ramsey\\Uuid\\Validator\\GenericValidator`.
This validator ensures the string is 36 characters, has the dashes in the correct places, and uses only hexadecimal
values. It does not ensure the string is of the `RFC 9562`_ (formerly `RFC 4122`_) variant or contains a valid version.

The validator :php:class:`Ramsey\\Uuid\\Rfc4122\\Validator` validates UUID strings to ensure they match the `RFC 9562`_
(formerly `RFC 4122`_) variant and contain a valid version. Since it is not enabled by default, you will need to
configure ramsey/uuid to use it, if you want stricter validation.

.. code-block:: php
    :caption: Set an alternate validator to use for Uuid::isValid()
    :name: customize.validators-example

    use Ramsey\Uuid\Rfc4122\Validator as Rfc4122Validator;
    use Ramsey\Uuid\Uuid;
    use Ramsey\Uuid\UuidFactory;

    $factory = new UuidFactory();
    $factory->setValidator(new Rfc4122Validator());

    Uuid::setFactory($factory);

    if (!Uuid::isValid('2bfb5006-087b-9553-5082-e8f39337ad29')) {
        echo "This UUID is not valid!\n";
    }

.. tip::

    If you want to use your own validation, create a class that implements :php:interface:`Ramsey\\Uuid\\Validator\\ValidatorInterface`
    and use the same method to set your validator on the factory.

.. _RFC 4122: https://www.rfc-editor.org/rfc/rfc4122
.. _RFC 9562: https://www.rfc-editor.org/rfc/rfc9562
