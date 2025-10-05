.. _database:

===================
Using In a Database
===================

.. tip::

    `ramsey/uuid-doctrine`_ allows the use of ramsey/uuid as a `Doctrine field type`_. If you use Doctrine, it's a great
    option for working with UUIDs and databases.

There are several strategies to consider when working with UUIDs in a database. Among these are whether to store the
string representation or bytes and whether the UUID column should be treated as a primary key. We'll discuss a few of
these approaches here, but the final decision on how to use UUIDs in a database is up to you since your needs will be
different from those of others.

.. note::

    All database code examples in this section assume the use of `MariaDB`_ and `PHP Data Objects (PDO)`_. If using a
    different database engine or connection library, your code will differ, but the general concepts should remain the
    same.

.. _database.string:

Storing As a String
###################

Perhaps the easiest way to store a UUID to a database is to create a ``char(36)`` column and store the UUID as a string.
When stored as a string, UUIDs require no special treatment in SQL statements or when displaying them.

The primary drawback is the size. At 36 characters, UUIDs can take up a lot of space, and when handling a lot of data,
this can add up.

.. code-block:: sql
    :caption: Create a table with a column for UUIDs
    :name: database.uuid-column-example

    CREATE TABLE `notes` (
        `uuid` char(36) NOT NULL,
        `notes` text NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

Using this database table, we can store the string UUID using code similar to this (assume some of the variables in this
example have been set beforehand):

.. code-block:: php
    :caption: Store a string UUID to the uuid column
    :name: database.uuid-column-store-example

    use Ramsey\Uuid\Uuid;

    $uuid = Uuid::uuid4();

    $dbh = new PDO($dsn, $username, $password);

    $sth = $dbh->prepare('
        INSERT INTO notes (
            uuid,
            notes
        ) VALUES (
            :uuid,
            :notes
        )
    ');

    $sth->execute([
        ':uuid' => $uuid->toString(),
        ':notes' => $notes,
    ]);

.. _database.bytes:

Storing As Bytes
################

In :ref:`the previous example <database.uuid-column-store-example>`, we saw how to store the string representation of a
UUID to a ``char(36)`` column. As discussed, the primary drawback is the size. However, if we store the UUID in byte
form, we only need a ``char(16)`` column, saving over half the space.

The primary drawback with this approach is ease-of-use. Since the UUID bytes are stored in the database, querying and
selecting data becomes more difficult.

.. code-block:: sql
    :caption: Create a table with a column for UUID bytes
    :name: database.uuid-bytes-example

    CREATE TABLE `notes` (
        `uuid` char(16) NOT NULL,
        `notes` text NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

Using this database table, we can store the UUID bytes using code similar to this (again, assume some of the variables
in this example have been set beforehand):

.. code-block:: php
    :caption: Store UUID bytes to the uuid column
    :name: database.uuid-bytes-store-example

    $sth->execute([
        ':uuid' => $uuid->getBytes(),
        ':notes' => $notes,
    ]);

Now, when we ``SELECT`` the records from the database, we will need to convert the ``notes.uuid`` column to a
ramsey/uuid object, so that we are able to use it.

.. code-block:: php
    :caption: Covert database UUID bytes to UuidInterface instance
    :name: database.uuid-bytes-convert-example

    use Ramsey\Uuid\Uuid;

    $uuid = Uuid::uuid4();

    $dbh = new PDO($dsn, $username, $password);

    $sth = $dbh->prepare('SELECT uuid, notes FROM notes');
    $sth->execute();

    foreach ($sth->fetchAll() as $record) {
        $uuid = Uuid::fromBytes($record['uuid']);

        printf(
            "UUID: %s\nNotes: %s\n\n",
            $uuid->toString(),
            $record['notes']
        );
    }

We'll also need to query the database using the bytes.

.. code-block:: php
    :caption: Look-up the record from the database, using the UUID bytes
    :name: database.uuid-bytes-select-example

    use Ramsey\Uuid\Uuid;

    $uuid = Uuid::fromString('278198d3-fa96-4833-abab-82f9e67f4712');

    $dbh = new PDO($dsn, $username, $password);

    $sth = $dbh->prepare('
        SELECT uuid, notes
        FROM notes
        WHERE uuid = :uuid
    ');

    $sth->execute([
        ':uuid' => $uuid->getBytes(),
    ]);

    $record = $sth->fetch();

    if ($record) {
        $uuid = Uuid::fromBytes($record['uuid']);

        printf(
            "UUID: %s\nNotes: %s\n\n",
            $uuid->toString(),
            $record['notes']
        );
    }

.. _database.pk:

Using As a Primary Key
######################

In the previous examples, we didn't use the UUID as a primary key, but it's logical to use the ``notes.uuid`` field as a
primary key. There's nothing wrong with this approach, but there are a couple of points to consider:

* InnoDB stores data in the primary key order
* All the secondary keys also contain the primary key (in InnoDB)

We'll deal with the first point in the section, :ref:`database.order`. For the second point, if you are using the string
version of the UUID (i.e., ``char(36)``), then not only will the primary key be large and take up a lot of space, but
every secondary key that uses that primary key will also be much larger.

For this reason, if you choose to use UUIDs as primary keys, it might be worth the drawbacks to use UUID bytes (i.e.,
``char(16)``) instead of the string representation (see :ref:`database.bytes`).

.. hint::

    If not using InnoDB with MySQL or MariaDB, consult your database engine documentation to find whether it also has
    similar properties that will factor into your use of UUIDs.

.. _database.uk:

Using As a Unique Key
#####################

Instead of :ref:`using UUIDs as a primary key <database.pk>`, you may choose to use an ``AUTO_INCREMENT`` column with
the ``int unsigned`` data type as a primary key, while using a ``char(36)`` for UUIDs and setting a ``UNIQUE KEY`` on
this column. This will aid in lookups while helping keep your secondary keys small.

.. code-block:: sql
    :caption: Use an auto-incrementing column as primary key, with UUID as a unique key
    :name: database.id-auto-increment-uuid-unique-key

    CREATE TABLE `notes` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `uuid` char(36) NOT NULL,
        `notes` text NOT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `notes_uuid_uk` (`uuid`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

.. _database.order:

Insertion Order and Sorting
###########################

UUID versions 1, 2, 3, 4, and 5 are not *monotonically increasing*. If using these versions as primary keys, the inserts
will be random, and the data will be scattered on disk (for InnoDB). Over time, as the database size grows, lookups will
become slower and slower.

.. tip::

    See Percona's "`Storing UUID Values in MySQL`_" post, for more details on the performance of UUIDs as primary keys.

To minimize these problems, two solutions have been devised:

1. :ref:`rfc4122.version6` UUIDs
2. :ref:`rfc4122.version7` UUIDs

.. note::

    We previously recommended the use of the :ref:`timestamp-first COMB <customize.timestamp-first-comb-codec>` or
    :ref:`ordered-time <customize.ordered-time-codec>` codecs to solve these problems. However, UUID versions 6 and 7
    were defined to provide these solutions in a standardized way.

.. _ramsey/uuid-doctrine: https://github.com/ramsey/uuid-doctrine
.. _Doctrine field type: https://www.doctrine-project.org/projects/doctrine-dbal/en/stable/reference/types.html
.. _MariaDB: https://mariadb.org
.. _PHP Data Objects (PDO): https://www.php.net/pdo
.. _Storing UUID Values in MySQL: https://www.percona.com/blog/store-uuid-optimized-way/
