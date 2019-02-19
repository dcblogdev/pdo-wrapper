# PDO Wrapper

This PDO wrapper, is a collection of crud methods for working with a database this includes selecting, inserting, updating and deleting records.

## Install

To install place this class into the project folder and include the class, then set the db credentials. Finally create an instance of the classes by calling it's get method.

This wrapper makes use of a single database connection further connections attempts will reuse the already open connections, if not already connected.

````php
include('database.php');

//db properties
define('DB_TYPE','mysql');
define('DB_HOST','localhost');
define('DB_USER','username');
define('DB_PASS','password');
define('DB_NAME','database name');

// make a connection to mysql here
$db = Database::get();
````

To make a connection to another database pass an array containing the following:

````
$db = Database::get(array(
	'type' => 'mysql',
	'host' => 'localhost',
	'name' => 'dbname',
	'user' => 'dbusername'
	'pass' => 'password'
));
````

# Usage examples

## Select:

````php
$db->select("column FROM table");
````

To select data based on user data instead of passing the data to the query directly use a prepared statement, this is safer and stops any attempt at sql injections.

````php
$db->select("username FROM members WHERE memberID = :id and email = :email", array(':id' => 1, ':email' => 'someone@domain.com'));
````

The above query will return the username from the members table where the memberID and email match. The memberID and email is passed seperartly in an array.

Instead of passing in an id and email to the query directly a placeholder is used :id and :email then an array is passed the keys in the array matches the placeholder and is bound, so the database will get both the query and the bound data.

Data returned from the query will be returns as an object this can be changed by passing a third param to the select containing PDO::FETCH_ASSOC.

To use the object loop through it, a typical example:

````php
$rows = $db->select("firstName, lastName FROM members ORDER BY firstName, lastName");
foreach ($rows as $row) {
    echo "<p>$row->firstName $row->lastName</p>";
}
````

## Select Signle Record:

Using find() will return only a single result. Like select it accepts params being passed in an array as a second argument.

````php
$db->find("column FROM table where id=:id", [':id' => 23]);
````

# Raw

A raw query is a query that is not ran through a prepared statement and will execute the query passed directly. Useful when creating a table.

````php
$db->raw("CREATE TABLE IF NOT EXISTS members (
  memberID INT(11) NOT NULL AUTO_INCREMENT,
  firstName VARCHAR(255) NOT NULL,
  lastnName VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  PRIMARY KEY (memberID))"
);
````

## Insert

Data is inserted by calling the insert method it expects the table name followed by an array of key and values to insert in to the database.

````php
$data = array(
    'firstName' => 'Joe',
    'lastnName' => 'Smith',
    'email' => 'someone@domain.com'
);
$db->insert('members', $data);
````

The insert automatically returns the last inserted id by returning 'lastInsertId' to collect the id:

````php
$id = $db->insert('members', $data);
````

## Updating

To update an existing record the update method is called. This method expects the table, array of data to update and a second array containing the where condition.

````php
$data = array(
    'firstName' => 'Joe',
    'lastnName' => 'Smith',
    'email' => 'someone@domain.com'
);
$where = array('memberID' => 2);
$db->update('members', $data, $where);
````
Or:

```php
$update = array( 
	'data'=>array(
	    'firstName' => 'Joe',
	    'lastnName' => 'Smith',
	    'email' => 'someone@domain.com'
		),
	'where'=> array('memberID' => 2)
	);

$db->update('members', $update['data'], $update['where']);

```

## Delete

To delete records call the delete method. This method expects the table name and an array of the where condition.

````php
$where = array('memberID' => 2);
$db->delete('members', $where);
````

This will delete a single record to set the limit pass a third parameters containing the number to limit to, or to remove the limit pass null as a third param.

````php
$db->delete('members', $where, 10);  //delete 10 records matcing the where
$db->delete('members', $where, null); //delete all records matching the where
```

## Truncate

To empty a table of all contents call the truncate method. Passing only the table name.

````php
$db->truncate('members');
````


## Count

To count records call the count method. This method expects the table name and column name (optional).

````php
$db->count('members');
````

If table has no column `id`
````
$db->count('members', 'member_id');
````

