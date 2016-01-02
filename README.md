#PDO Wrapper

This PDO wrapper, is a collection of crud methods for working with a database this includes selecting, inserting, updating and deleting records.

##Install

To install place this class into the project folder and include the class, the set the db credentials. Finally create an instance of the classes by calling it's get method.

This wrapper makes use of a single database connection further connections attempts will reuse the already open connections, if not already connected.

````
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

#Usage examples

##Select:

````
$db->select("SELECT column FROM table");
````

To select data based on user data instead of passing the data to the query directly use a prepared statement, this is safer and stops any attempt at sql injections.

````
$db->select("SELECT username FROM members WHERE memberID = :id and email = :email", array(':id' => 1, ':email' => 'someone@domain.com'));
````

The above query will return the username from the members table where the memberID and email match. The memberID and email is passed seperartly in an array.

Data returned from the query will be returns as an object this can be changed by passing a third param to the select containing PDO::FETCH_ASSOC.

To use the object loop through it, a typical example:

````
$rows = $db->select("SELCET * FROM members ORDER BY firstName, lastName");
foreach ($rows as $row) {
    echo "<p>$row->firstName $row->lastName</p>";
}
````

#Raw

A raw query is a query that is not ran through a prepared statement and will execute the query passed directly. Useful when crating a table.

````
$db->raw("CREATE TABLE IF NOT EXISTS members (
  memberID INT(11) NOT NULL AUTO_INCREMENT,
  username VARCHAR(255) NOT NULL,
  password VARCHAR(255) NOT NULL,
  PRIMARY KEY (memberID))"
);
````

##Insert

Data is inserted by calling the insert method it expects the table name followed by an array of key and values to insert in to the database.

````
$data = array(
    'firstName' => 'Joe',
    'lastnName' => 'Smith',
    'email' => 'someone@domain.com'
);
$db->insert('members', $data);
````

The insert automatically returns the last inserted id by returning 'lastInsertId' to collect the id:

````
$id = $db->insert('members', $data);
````

##Updating

To update an existing record the update method is called. This method expects the table, array of data to update and a second array containing the where condition.

````
$data = array(
    'firstName' => 'Joe',
    'lastnName' => 'Smith',
    'email' => 'someone@domain.com'
);
$where = array('memberID' => 2);
$db->update('members', $data, $where);
````

##Delete

To delete records call the delete method. This method expects the table name and an array of the where condition.

````
$where = array('memberID' => 2);
$db->delect('members', $where);
````

This will delete a single record to set the limit pass a third parameters containing the number to limit to, or to remove the limit pass null as a third param.

````
$db->delete('members', $where, 10);  //delete 10 records matcing the where
$db->delete('members', $where, null); //delete all records matching the where

##Truncate

To empty a table of all contents call the truncate method. Passing only the table name.

````
$db->truncate('members');
````




























