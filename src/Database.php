<?php
namespace Daveismyname\PdoWrapper;

use PDO;

class Database extends PDO
{
    /**
     * @var array Array of saved databases for reusing
     */
    protected static $instances = [];

    /**
     * Static method get
     *
     * @param  array $group
     * @return database
     */
    public static function get(string $username, string $password, string $database, string $host = 'localhost', string $type = 'mysql')
    {
        // ID for database based on the credentials
        $id = "$type.$host.$database.$username.$password";

        // Checking if the same
        if (isset(self::$instances[$id])) {
            return self::$instances[$id];
        }

        $instance = new Database("$type:host=$host;dbname=$database;charset=utf8", $username, $password);
        $instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Setting Database into $instances to avoid duplication
        self::$instances[$id] = $instance;

        //return the pdo instance
        return $instance;

    }

    /**
     * run raw sql queries
     * @param  string $sql sql command
     * @return none
     */
    public function raw(string $sql)
    {
        $stmt = $this->query($sql);
    }

    /**
     * method for selecting records from a database
     * @param  string $sql       sql query
     * @param  array  $array     named params
     * @param  object $fetchMode
     * @param  string $class     class name
     * @param  string $single    when set will return only 1 record
     * @return array            returns an array of records
     */
    public function select(string $sql, array $array = [], int $fetchMode = PDO::FETCH_OBJ, string $class = '', bool $single = false)
    {
         // Append select if it isn't appended.
        if (strtolower(substr($sql, 0, 7)) !== 'select ') {
            $sql = "SELECT " . $sql;
        }

        $stmt = $this->prepare($sql);

        //if array has named placeholders
        if ($this->has_string_keys($array)) {
            foreach ($array as $key => $value) {
                if (is_int($value)) {
                    $stmt->bindValue("$key", $value, PDO::PARAM_INT);
                } else {
                    $stmt->bindValue("$key", $value);
                }
            }

            $stmt->execute();
        } else {
            //for ? placeholders pass the array
            $stmt->execute($array);
        }

        if ($single == false) {
            return $fetchMode === PDO::FETCH_CLASS ? $stmt->fetchAll($fetchMode, $class) : $stmt->fetchAll($fetchMode);
        } else {
            return $fetchMode === PDO::FETCH_CLASS ? $stmt->fetch($fetchMode, $class) : $stmt->fetch($fetchMode);
        }
    }

    /**
     * Fetch a single record
     * @param  string $sql       sql query
     * @param  array  $array     named params
     * @param  object $fetchMode
     * @param  string $class     class name
     * @return array            returns a single record
     */
    public function find($sql, $array = [], $fetchMode = PDO::FETCH_OBJ, $class = '')
    {
        return $this->select($sql, $array, $fetchMode, $class, $single = true);
    }

    /**
    * Count method
    * @param  string $table table name
    * @param  string $column optional
    */
    public function count($table, $column= 'id')
    {
        $stmt = $this->prepare("SELECT $column FROM $table");
        $stmt->execute();
        return $stmt->rowCount();
    }

    /**
     * insert method
     * @param  string $table table name
     * @param  array $data  array of columns and values
     */
    public function insert($table, $data)
    {
        ksort($data);

        $fieldNames = implode(',', array_keys($data));
        $fieldValues = ':'.implode(', :', array_keys($data));

        $stmt = $this->prepare("INSERT INTO $table ($fieldNames) VALUES ($fieldValues)");

        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        $stmt->execute();
        return $this->lastInsertId();
    }

    /**
     * update method
     * @param  string $table table name
     * @param  array $data  array of columns and values
     * @param  array $where array of columns and values
     */
    public function update($table, $data, $where)
    {
        ksort($data);

        $fieldDetails = null;
        foreach ($data as $key => $value) {
            $fieldDetails .= "$key = :d_$key,";
        }
        $fieldDetails = rtrim($fieldDetails, ',');

        $whereDetails = null;
        $i = 0;
        foreach ($where as $key => $value) {
            if ($i == 0) {
                $whereDetails .= "$key = :w_$key";
            } else {
                $whereDetails .= " AND $key = :w_$key";
            }
            $i++;
        }
        $whereDetails = ltrim($whereDetails, ' AND ');

        $stmt = $this->prepare("UPDATE $table SET $fieldDetails WHERE $whereDetails");

        foreach ($data as $key => $value) {
            $stmt->bindValue(":d_$key", $value);
        }

        foreach ($where as $key => $value) {
            $stmt->bindValue(":w_$key", $value);
        }

        $stmt->execute();
        return $stmt->rowCount();
    }

    /**
     * Delete method
     * @param  string $table table name
     * @param  array $data  array of columns and values
     * @param  array $where array of columns and values
     * @param  integer $limit limit number of records
     */
    public function delete($table, $where, $limit = 1)
    {
        ksort($where);

        $whereDetails = null;
        $i = 0;
        foreach ($where as $key => $value) {
            if ($i == 0) {
                $whereDetails .= "$key = :$key";
            } else {
                $whereDetails .= " AND $key = :$key";
            }
            $i++;
        }
        $whereDetails = ltrim($whereDetails, ' AND ');

        //if limit is a number use a limit on the query
        if (is_numeric($limit)) {
            $uselimit = "LIMIT $limit";
        }

        $stmt = $this->prepare("DELETE FROM $table WHERE $whereDetails $uselimit");

        foreach ($where as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        $stmt->execute();
        return $stmt->rowCount();
    }

    public function deleteByIds(string $table, string $column, string $ids)
    {
        $stmt = $this->prepare("DELETE FROM $table WHERE $column IN ($ids)");
        $stmt->execute();
        return $stmt->rowCount();        
    }

    /**
     * truncate table
     * @param  string $table table name
     */
    public function truncate($table)
    {
        return $this->exec("TRUNCATE TABLE $table");
    }

    /**
     * Determine if an array holds string keys 
     * @param  array   $array 
     * @return boolean
     */
    protected function has_string_keys(array $array) 
    {
        return count(array_filter(array_keys($array), 'is_string')) > 0;
    }
}
