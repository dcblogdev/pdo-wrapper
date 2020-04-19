# PDO Wrapper

This PDO wrapper, is a collection of methods for working with a database this includes selecting, inserting, updating and deleting records.

## Install

Using composer include the repository by typing the following into a terminal

```
composer require dcblogdev/pdo-wrapper
```

Set the db credentials. Finally create an instance of the classes.

```php
use Dcblogdev\PdoWrapper\Database;

// make a connection to mysql here
$options = [
    //required
    'username' => '',
    'database' => '',
    //optional
    'password' => '',
    'type' => 'mysql',
    'charset' => 'utf8',
    'host' => 'localhost',
    'port' => '3309'
];

$db = new Database($options);
```

# Usage examples

## Accessing PDO

You can call getPdo()` to get access to PDO directly:

```php
$db->getPdo()
```

This allows to chain calls:

```php
$db->getPdo()->query($sql)->fetch();
```


## querying:

All quries use prepared statements, calling `->run()` returns a PDO option that can be chained:

Select multiple records:

```php
$db->run("select * FROM users")->fetchAll();
```

Select a single record:

```php
$db->run("select * FROM users")->fetch();
```

Select multiple records using `->rows`

```php
$db->rows("select * FROM table");
```

Select single record using `->row`

```php
$db->row("select * FROM table");
```

To select records based on user data instead of passing the data to the query directly use a prepared statement, this is safer and stops any attempt at sql injections.

**Names placeholders**

```php
$db->row("select username FROM users WHERE id = :id and email = :email", ['id' => 1, ':email' => 'someone@domain.com']);
```

**Annonomus placeholders**

```php
$db->row("select username FROM users WHERE id = ? and email = ?", [1, 'someone@domain.com']);
```

The above query will return the username from a users table where the id and email match. The id and email is passed seperartly in an array.

Instead of passing in an id and email to the query directly a placeholder is used :id and :email (or ? can be used) then an array is passed the keys in the array matches the placeholder and is bound, so the database will get both the query and the bound data.

Data returned from the query will be returns as an object this can be changed by passing a third param containing PDO::FETCH_ASSOC.

To use the object loop through it, a typical example:

```php
$rows = $db->rows("firstName, lastName FROM username ORDER BY firstName, lastName");
foreach ($rows as $row) {
    echo "<p>$row->firstName $row->lastName</p>";
}
```

## Select Single Record:

Using row() will return only a single result. Like rows it accepts params being passed in an array as a second argument.

**Names placeholders**

```php
$db->row("column FROM table where id=:id", ['id' => 23]);
```

**Annonomus placeholders**

```php
$db->row("column FROM table where id=?", [23]);
```

# Raw

A raw query is a query that is not ran through a prepared statement and will execute the query passed directly. Useful when creating a table.

```php
$db->raw("CREATE TABLE IF NOT EXISTS members (
  memberID INT(11) NOT NULL AUTO_INCREMENT,
  firstName VARCHAR(255) NOT NULL,
  lastnName VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  PRIMARY KEY (memberID))"
);
```

## Count

To count records call the count method. This method expects the table name and column name (optional).

```php
$db->count('users');
```

If table has no column `id`

```php
$db->count('users', 'user_id');
```

## Insert

Data is inserted by calling the insert method it expects the table name followed by an array of key and values to insert in to the database.

```php
$data = [
    'firstName' => 'Joe',
    'lastnName' => 'Smith',
    'email' => 'someone@domain.com'
];
$db->insert('users', $data);
```

The insert automatically returns the last inserted id by returning 'lastInsertId' to collect the id:

```php
$id = $db->insert('users', $data);
```

## Updating

To update an existing record the update method is called. This method expects the table, array of data to update and a second array containing the where condition.

```php
$data = [
    'firstName' => 'Joe',
    'lastnName' => 'Smith',
    'email' => 'someone@domain.com'
];
$where = ['id' => 2];
$db->update('users', $data, $where);
```
Or:

```php
$update = [ 
	'data' => [
	    'firstName' => 'Joe',
	    'lastnName' => 'Smith',
	    'email' => 'someone@domain.com'
	],
	'where' => [
        'id' => 2
    ]
];

$db->update('users', $update['data'], $update['where']);

```

## Delete

To delete records call the delete method. This method expects the table name and an array of the where condition.

```php
$where = ['id' => 2];
$db->delete('users', $where);
```

This will delete a single record to set the limit pass a third parameters containing the number to limit to, or to remove the limit pass null as a third param.

```php
$db->delete('users', $where, 10);  //delete 10 records matcing the where
$db->delete('users', $where, null); //delete all records matching the where
```

## Delete multiple IN

To delete multiple records where ids are in a specific column, this uses WHERE id IN (4,5,6)

```php
$db->deleteByIds('users', 'id', '4,5,6');
```

## Truncate

To empty a table of all contents call the truncate method. Passing only the table name.

```php
$db->truncate('users');
```