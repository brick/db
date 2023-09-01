Brick\Db
========

<img src="https://raw.githubusercontent.com/brick/brick/master/logo.png" alt="" align="left" height="64">

A collection of helper tools for interacting with databases.

[![Build Status](https://github.com/brick/db/workflows/CI/badge.svg)](https://github.com/brick/db/actions)
[![Coverage Status](https://coveralls.io/repos/github/brick/db/badge.svg?branch=master)](https://coveralls.io/github/brick/db?branch=master)
[![Latest Stable Version](https://poser.pugx.org/brick/db/v/stable)](https://packagist.org/packages/brick/db)
[![Total Downloads](https://poser.pugx.org/brick/db/downloads)](https://packagist.org/packages/brick/db)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](http://opensource.org/licenses/MIT)

Installation
------------

This library is installable via [Composer](https://getcomposer.org/):

```bash
composer require brick/db
```

Requirements
------------

This library requires PHP 7.4 or later. 

For PHP 7.1, 7.2 & 7.3 compatibility, you can use version `0.1`. Note that [these PHP versions are EOL](http://php.net/supported-versions.php) and not supported anymore. If you're still using one of these PHP versions, you should consider upgrading as soon as possible.

Package overview
----------------

This package contains two helpers: `BulkInserter` and `BulkDeleter`. These classes, built on top of `PDO`, allow you to speed up database
rows insertion & deletion by performing multiple operations per query, with a clean OO API.

### BulkInserter

This class takes advantage of the extended insert / multirow syntax available in MySQL, PostgreSQL and SQLite.

It basically replaces the need to send a batch of queries:

```sql
INSERT INTO user (id, name, age) VALUES (1, 'Bob', 20);
INSERT INTO user (id, name, age) VALUES (2, 'John', 22);
INSERT INTO user (id, name, age) VALUES (3, 'Alice', 24);
```

with a single, faster query:

```sql
INSERT INTO user (id, name, age) VALUES (1, 'Bob', 20), (2, 'John', 22), (3, 'Alice', 24);
```

To use it, create a `BulkInserter` instance with:

- your `PDO` connection object
- the name of your table
- the name of the columns to insert
- the number of inserts to perform per query (optional, defaults to 100)

#### Example

```php
use Brick\Db\Bulk\BulkInserter;

$pdo = new PDO(...);
$inserter = new BulkInserter($pdo, 'user', ['id', 'name', 'age'], 100);

$inserter->queue(1, 'Bob', 20);
$inserter->queue(2, 'John', 22);
$inserter->queue(3, 'Alice', 24);

$inserter->flush();
```

The `queue()` method does not do anything until either `flush()` is called, or the number of inserts per query is reached.

*Note: `queue()` returns `false` when the insert has been queued only, and `true` when the number of inserts per query has been reached and the batch has therefore been flushed to the database. This can be useful to monitor the progress of the batch.*

**Do not forget to call `flush()` after all your inserts have been queued. Failure to do so would result in records not being inserted.**


### BulkDeleter

This class allows you to delete multiple records at a time.

It basically replaces the need for these queries:

```sql
DELETE FROM user WHERE id = 1;
DELETE FROM user WHERE id = 2;
DELETE FROM user WHERE id = 3;
```

with a single, faster query:

```sql
DELETE FROM user WHERE (id = 1) OR (id = 2) OR (id = 3);
```

The constructor parameters are the same as `BulkInserter`.

For obvious performance reasons, the list of columns used to identify a record should match the primary key or a unique index of the table.


#### Example

With a single column primary key / unique index:

```php
use Brick\Db\Bulk\BulkDeleter;

$pdo = new PDO(...);
$deleter = new BulkDeleter($pdo, 'user', ['id']);

$deleter->queue(1);
$deleter->queue(2);
$deleter->queue(3);

$deleter->flush();
```

With a composite key:

```php
use Brick\Db\Bulk\BulkDeleter;

$pdo = new PDO(...);
$deleter = new BulkDeleter($pdo, 'user_product', ['user_id', 'product_id]);

$deleter->queue(1, 123);
$deleter->queue(2, 456);
$deleter->queue(3, 789);

$deleter->flush();
```

**Do not forget to call `flush()` after all your deletes have been queued. Failure to do so would result in records not being deleted.**

### Performance tips

To get the maximum performance out of this library, you should:

- wrap your operations in a transaction
- disable emulation of prepared statements (`PDO::ATTR_EMULATE_PREPARES=false`)

These two tips combined can get you **up to 50% more throughput** in terms of inserts per second. Sample code:

```php
$pdo = new PDO(...);
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

$inserter = new BulkInserter($pdo, 'user', ['id', 'name', 'age']);
$pdo->beginTransaction();

$inserter->queue(...);
// more queue() calls...

$inserter->flush();
$pdo->commit();

```

The library could do this automatically, but doesn't for the following reasons:

- your PDO object's configuration should not be modified by a third-party library
- you should have full control over your transactions, when to start them and when to commit them

### Respecting the limits

Be careful when raising the number of operations per query, as you might hit these limits:

- PHP's [memory_limit](http://php.net/manual/en/ini.core.php#ini.memory-limit)
- MySQL's [max_allowed_packet](https://dev.mysql.com/doc/refman/5.7/en/packet-too-large.html)

You can tweak these settings if you have access to your server's configuration, however it's important to benchmark with different batch sizes, to determine the optimal size and see if increasing the server limits is worth the effort.
In most cases, 100 inserts per query should give you at least 80% of the maximum throughput:

![Extended inserts benchmark](https://cdn-images-1.medium.com/max/800/1*k_QS1qtgN5-UyrDkjSRg_w.png)

See [this article](https://medium.com/@benmorel/high-speed-inserts-with-mysql-9d3dcd76f723) for a more in-depth analysis.

MySQL also has a limit of 65535 placeholders per statement, effectively limiting the number of operations per query to `floor(65535 / number of columns)`. This does not apply if PDO emulates prepared statements.
