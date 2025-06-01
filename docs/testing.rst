.. _testing:

==================
Testing With UUIDs
==================

One problem with the use of ``final`` is the inability to create a `mock object`_ to use in tests. However, the
following techniques should help with testing.

.. tip::

    To learn why ramsey/uuid uses ``final``, take a look at :ref:`faq.final`.

.. _testing.inject:

Inject a UUID of a Specific Type
--------------------------------

Let's say we have a method that uses a type hint for :php:class:`UuidV1 <Ramsey\\Uuid\\Rfc4122\\UuidV1>`.

.. code-block:: php

    public function tellTime(UuidV1 $uuid): string
    {
        return $uuid->getDateTime()->format('Y-m-d H:i:s');
    }

Since this method uses UuidV1 as the type hint, we're not able to pass another object that implements UuidInterface, and
we cannot extend or mock UuidV1, so how do we test this?

One way is to use :php:meth:`Uuid::uuid1() <Ramsey\\Uuid\\Uuid::uuid1>` to create a regular UuidV1 instance and pass it.

.. code-block:: php

    public function testTellTime(): void
    {
        $uuid = Uuid::uuid1();
        $myObj = new MyClass();

        $this->assertIsString($myObj->tellTime($uuid));
    }

This might satisfy our testing needs if we only want to assert that the method returns a string. If we want to test for
a specific string, we can do that, too, by generating a UUID ahead of time and using it as a known value.

.. code-block:: php

    public function testTellTime(): void
    {
        // We generated this version 1 UUID ahead of time and know the
        // exact date and time it contains, so we can use it to test the
        // return value of our method.
        $uuid = Uuid::fromString('177ef0d8-6630-11ea-b69a-0242ac130003');
        $myObj = new MyClass();

        $this->assertSame('2020-03-14 20:12:12', $myObj->tellTime($uuid));
    }

.. note::

    These examples assume the use of `PHPUnit`_ for tests. The concepts will work no matter what testing framework you use.

.. _testing.static:

Returning Specific UUIDs From a Static Method
---------------------------------------------

Sometimes, rather than pass UUIDs as method arguments, we might call the static methods on the Uuid class from inside
the method we want to test. This can be tricky to test.

.. code-block:: php

    public function tellTime(): string
    {
        $uuid = Uuid::uuid1();

        return $uuid->getDateTime()->format('Y-m-d H:i:s');
    }

We can call this in a test and assert that it returns a string, but we can't return a specific UUID value from the
static method call --- or can we?

We can do this by :ref:`overriding the default factory <customize.factory>`.

First, we create our own factory class for testing. In this example, we extend UuidFactory, but you may create your own
separate factory class for testing, as long as you implement :php:interface:`Ramsey\\Uuid\\UuidFactoryInterface`.

.. code-block:: php

    namespace MyPackage;

    use Ramsey\Uuid\UuidFactory;
    use Ramsey\Uuid\UuidInterface;

    class MyTestUuidFactory extends UuidFactory
    {
        public $uuid1;

        public function uuid1($node = null, ?int $clockSeq = null): UuidInterface
        {
            return $this->uuid1;
        }
    }

Now, from our tests, we can replace the default factory with our new factory, and we can even change the value returned
by the :php:meth:`uuid1() <Ramsey\\Uuid\\UuidFactoryInterface::uuid1>` method for our tests.

.. code-block:: php

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testTellTime(): void
    {
        $factory = new MyTestUuidFactory();
        Uuid::setFactory($factory);

        $myObj = new MyClass();

        $factory->uuid1 = Uuid::fromString('177ef0d8-6630-11ea-b69a-0242ac130003');
        $this->assertSame('2020-03-14 20:12:12', $myObj->tellTime());

        $factory->uuid1 = Uuid::fromString('13814000-1dd2-11b2-9669-00007ffffffe');
        $this->assertSame('1970-01-01 00:00:00', $myObj->tellTime());
    }

.. attention::

    The factory is a static property on the Uuid class. By replacing it like this, all uses of the Uuid class after this
    point will continue to use the new factory. This is why we must run the test in a separate process. Otherwise, this
    could cause other tests to fail.

    Running tests in separate processes can significantly slow down your tests, so try to use this technique sparingly,
    and if possible, pass your dependencies to your objects, rather than creating (or fetching them) from within. This
    makes testing easier.


.. _testing.mock:

Mocking UuidInterface
---------------------

Another technique for testing with UUIDs is to mock :php:interface:`UuidInterface <Ramsey\\Uuid\\UuidInterface>`.

Consider a method that accepts a UuidInterface.

.. code-block:: php

    public function tellTime(UuidInterface $uuid): string
    {
        return $uuid->getDateTime()->format('Y-m-d H:i:s');
    }

We can mock UuidInterface, passing that mocked value into this method. Then, we can make assertions about what methods
were called on the mock object. In the following example test, we don't care whether the return value matches an actual
date format. What we care about is that the methods on the UuidInterface object were called.

.. code-block:: php

    public function testTellTime(): void
    {
        $dateTime = Mockery::mock(DateTime::class);
        $dateTime->expects()->format('Y-m-d H:i:s')->andReturn('a test date');

        $uuid = Mockery::mock(UuidInterface::class, [
            'getDateTime' => $dateTime,
        ]);

        $myObj = new MyClass();

        $this->assertSame('a test date', $myObj->tellTime($uuid));
    }

.. note::

    One of my favorite mocking libraries is `Mockery`_, so that's what I use in these examples. However, other mocking
    libraries exist, and PHPUnit provides built-in mocking capabilities.

.. _mock object: https://en.wikipedia.org/wiki/Mock_object
.. _PHPUnit: https://phpunit.de
.. _Mockery: https://github.com/mockery/mockery
