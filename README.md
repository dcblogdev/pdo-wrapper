# PDO Wrapper

This PDO wrapper, is a collection of methods for working with a database this includes selecting, inserting, updating and deleting records.

> V2+ has been rewritten for the old docs please see [V1 branch](https://github.com/dcblogdev/pdo-wrapper/tree/v1)

## Upgrade from V1

Version 2 is now namespaced as `Dcblogdev` instead of `Daveismyname`

Also the methods `get()` and `select()` have been removed.

Instead of ::get() a new instance of the class used `new Database($args)`

Select has been replaced with `->rows()` and `->row()` or `->run()`

## Install
[![Latest Version on Packagist](https://img.shields.io/packagist/v/dcblogdev/pdo-wrapper.svg?style=flat-square)](https://packagist.org/packages/dcblogdev/pdo-wrapper)
[![Total Downloads](https://img.shields.io/packagist/dt/dcblogdev/pdo-wrapper.svg?style=flat-square)](https://packagist.org/packages/dcblogdev/pdo-wrapper)

![Logo](https://repository-images.githubusercontent.com/48907251/f6aff180-494c-11eb-8ca6-80000ee9dbf2)


This PDO wrapper, is a collection of methods for working with a database this includes selecting, inserting, updating and deleting records.

> V2+ has been rewritten for the old docs please see [V1 branch](https://github.com/dcblogdev/pdo-wrapper/tree/v1)

## Upgrade from V1

Version 2 is now namespaced as `Dcblogdev` instead of `Daveismyname`

Also the methods `get()` and `select()` have been removed.

Instead of ::get() a new instance of the class used `new Database($args)`

Select has been replaced with `->rows()` and `->row()` or `->run()`

# Documentation and install instructions 
[https://dcblog.dev/docs/pdo-wrapper](https://dcblog.dev/docs/pdo-wrapper)

# Quick Reference
```php
//create table
$db->raw("CREATE TABLE demo (id int auto_increment primary key, name varchar(255))");

//use PDO directly
$db->getPdo()->query('Select username FROM users')->fetchAll();

//use run to query and chain methods
$db->run("SELECT * FROM users")->fetchAll();
$db->run("SELECT * FROM users")->fetch();
$db->run("SELECT * FROM users WHERE id = ?", [$id])->fetch();
//select using array instead of object
$db->run("SELECT * FROM users")->fetch(PDO::FETCH_ASSOC);

//get by id
$db->getById('users', 2);

//get all rows
$db->rows("SELECT title FROM posts");
//get all rows with placeholders
$db->rows("SELECT title FROM posts WHERE user_id = ?", [$user_id]);

//get single row
$db->row("SELECT title FROM posts");
//get single row with placeholders
$db->row("SELECT title FROM posts WHERE user_id = ?", [$user_id]);

//count
$db->count("SELECT id FROM posts");
$db->count("SELECT id FROM posts WHERE category_id = ?", [$category_id]);

//insert
$id = $db->insert('users', ['username' => 'Dave', 'role' => 'Admin']);

//last inserted id
$db->lastInsertId()();

//update
$db->update('users', ['role' => 'Editor'], ['id' => 3]);

//delete from table with a where claus and a limit of 1 record
$db->delete('posts', ['type_id' => 'draft'], $limit = 1);

//delete from table with a where claus and a limit of 10 record
$db->delete('posts', ['type_id' => 'draft'], $limit = 10);

//delete all from table with a where claus and a limit of 10 record
$db->delete('posts', ['type_id' => 'draft'], null);

//delete all from table
$db->deleteAll('posts');

//delete by id from table
$db->deleteById('posts', 2);

//delete by ids from table
$db->deleteById('posts', '2,4,7');

//truncate table
$eb->truncate('posts');
```
