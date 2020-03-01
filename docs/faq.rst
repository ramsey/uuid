=================================
Frequently Asked Questions (FAQs)
=================================

.. contents::
    :local:

How do I fix "rhumsaa/uuid is abandoned" messages?
##################################################

When installing your project's dependencies using Composer, you might see the
following message:

.. code-block:: text

    Package rhumsaa/uuid is abandoned, you should avoid using it. Use
    ramsey/uuid instead.

Don't panic. Simply execute the following commands with Composer:

.. code-block:: bash

    composer remove rhumsaa/uuid
    composer require ramsey/uuid=^2.9

After doing so, you will have the latest ramsey/uuid package in the 2.x series,
and there will be no need to modify any code; the namespace in the 2.x series is
still ``Rhumsaa``.
