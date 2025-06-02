.. _faq:

=================================
Frequently Asked Questions (FAQs)
=================================

.. contents::
    :local:
    :depth: 1

.. _faq.rhumsaa-abandoned:

How do I fix "rhumsaa/uuid is abandoned" messages?
##################################################

When installing your project's dependencies using Composer, you might see the following message:

.. code-block:: text

    Package rhumsaa/uuid is abandoned; you should avoid using it. Use ramsey/uuid instead.

Don't panic. Simply execute the following commands with Composer:

.. code-block:: bash

    composer remove rhumsaa/uuid
    composer require ramsey/uuid=^2.9

After doing so, you will have the latest ramsey/uuid package in the 2.x series, and there will be no need to modify any
code; the namespace in the 2.x series is still ``Rhumsaa``.

.. _faq.final:

Why does ramsey/uuid use ``final``?
###################################

You might notice that many of the concrete classes returned in ramsey/uuid are marked as ``final``. There are specific
reasons for this choice, and I will offer a few solutions for those looking to extend or mock the classes for testing
purposes.

But Why?
--------

.. raw:: html

    <div style="width:100%;height:0;padding-bottom:56%;position:relative;">
        <iframe src="https://giphy.com/embed/eauCbbW6MvqKI" width="100%" height="100%" style="position:absolute" frameBorder="0" class="giphy-embed" allowFullScreen></iframe>
    </div>
    <p><a href="https://giphy.com/gifs/eauCbbW6MvqKI">via GIPHY</a></p>

First, let's take a look at why ramsey/uuid uses ``final``.

UUIDs are defined by a set of rules --- published as `RFC 9562`_ (formerly `RFC 4122`_) --- and those rules shouldn't
change. If they do, then it's no longer a UUID --- at least not as defined by `RFC 9562`_.

As an example, let's think about :php:class:`Rfc4122\\UuidV1 <Ramsey\\Uuid\\Rfc4122\\UuidV1>`. If our application wants
to do something special with this type, it might use the ``instanceof`` operator to check that a variable is a UuidV1,
or it might use a type hint on a method argument. If a third-party library passes a UUID object to us that extends
UuidV1 but overrides some very important internal logic, then we may no longer have a version 1 UUID. Perhaps we can all
be adults and play nicely, but ramsey/uuid cannot make any guarantees for any subclasses of UuidV1.

However, ramsey/uuid *can* make guarantees about classes that implement :php:interface:`UuidInterface <Ramsey\\Uuid\\UuidInterface>`
or :php:interface:`Rfc4122\\UuidInterface <Ramsey\\Uuid\\Rfc4122\\UuidInterface>`.

So, if we're working with an instance of a class that is marked ``final``, we can guarantee that the rules for the
creation of that object will not change, even if a third-party library passes us an instance of the same class.

This is the reason why ramsey/uuid specifies certain :ref:`argument and return types <reference.types>` that are marked
``final``. Since these are ``final``, ramsey/uuid is able to guarantee the type of data these value objects contain.
:php:class:`Type\\Integer <Ramsey\\Uuid\\Type\\Integer>` should never contain any characters other than numeral digits,
and :php:class:`Type\\Hexadecimal <Ramsey\\Uuid\\Type\\Hexadecimal>` should never contain any characters other than
hexadecimal digits. If other libraries could extend these and return them from UUID instances, then ramsey/uuid cannot
guarantee their values.

This is very similar to using strict types with ``int``, ``float``, or ``bool``. These types cannot change, so think of
final classes in ramsey/uuid as types that cannot change.

Overriding Behavior
-------------------

You may override the behavior of ramsey/uuid as much as you want. Despite the use of ``final``, the library is very
flexible. Take a look at the myriad opportunities to change how the library works:

* :ref:`rfc4122.version1.random`
* :ref:`customize.timestamp-first-comb-codec`
* :ref:`customize.factory`
* :ref:`And more... <customize>`

ramsey/uuid is able to provide this flexibility through the use of `interfaces`_, `factories`_, and `dependency injection`_.

At the same time, ramsey/uuid is able to guarantee that neither a :php:class:`UuidV1 <Ramsey\\Uuid\\Rfc4122\\UuidV1>`
nor a :php:class:`UuidV4 <Ramsey\\Uuid\\Rfc4122\\UuidV4>` nor an :php:class:`Integer <Ramsey\\Uuid\\Type\\Integer>` nor
a :php:class:`Time <Ramsey\\Uuid\\Type\\Time>`, etc. will ever change because of `downstream`_ code.

UUIDs have specific rules that make them practically unique. ramsey/uuid ensures that other code cannot change this
expectation while allowing your code and third-party libraries to change how UUIDs are generated and to return different
types of UUIDs not specified by `RFC 9562`_.

Testing With UUIDs
------------------

Sometimes, the use of ``final`` can throw a wrench in our ability to write tests, but it doesn't have to be that way. To
learn a few techniques for using ramsey/uuid instances in your tests, take a look at :ref:`testing`.

.. _RFC 4122: https://www.rfc-editor.org/rfc/rfc4122
.. _RFC 9562: https://www.rfc-editor.org/rfc/rfc9562
.. _interfaces: https://www.php.net/interfaces
.. _factories: https://en.wikipedia.org/wiki/Factory_%28object-oriented_programming%29
.. _dependency injection: https://en.wikipedia.org/wiki/Dependency_injection
.. _downstream: https://en.wikipedia.org/wiki/Downstream_(software_development)
