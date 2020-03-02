.. _installation:

======================
Installing ramsey/uuid
======================

Requirements
############

ramsey/uuid |version| requires PHP 7.2. Using the latest version of PHP is
highly recommended.

ramsey/uuid requires the `json <http://php.net/manual/en/json.installation.php>`_
extension, which is normally enabled by default.

ramsey/uuid also requires the
`pcre <http://php.net/manual/en/pcre.installation.php>`_
and `spl <http://php.net/manual/en/spl.installation.php>`_
extensions. These standard extensions are enabled by default and cannot be
disabled without patching PHP's build system and/or C sources.

ramsey/uuid recommends installing the
`ctype <https://www.php.net/manual/en/ctype.installation.php>`_,
`gmp <https://www.php.net/manual/en/gmp.installation.php>`_,
or `bcmath <https://www.php.net/manual/en/bc.installation.php>`_ extensions.
While not required, these extensions improve the performance of ramsey/uuid.

Composer
########

The only supported installation method for ramsey/uuid is
`Composer <https://getcomposer.org>`_. Use the following command to add
ramsey/uuid to your project dependencies:

.. code-block:: bash

    composer require ramsey/uuid
