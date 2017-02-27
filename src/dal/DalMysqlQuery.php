<?php

namespace Dal;

/**
 * Mysql implementation
 * @package Core
 */
class MysqlQuery extends AbstractQuery {

    /**
     * @var \mysqli_connection Mysql connection
     */
    public $connection = null;

    /**
     * @var \mysqli_result
     */
    public $result = null;

    /**
     * Construct query from configuration or another Dal\MysqlQuery object
     * @param object $init Configuration object (host, user, password, dbname) or Dal\MysqlQuery
     */
    public function __construct($init = null) {
        if (get_class($init) == 'Dal\MysqlQuery') {
            $this->cfg = $init->cfg;
            $this->connection = $init->connection;
        } else {
            $this->cfg = $init;
            if ($init) $this->connect();
        }
    }

    /**
     * Create new query with same connection
     * @return MysqlQuery
     */
    public function __invoke() {
        $this2 = new MysqlQuery($this);
        return $this2;
    }

    /**
     * Connect to database
     * @param object $cfg Configuration object (host, user, password, dbname)
     * @return \Dal\MysqlQuery
     * @throws \Dal\Exception
     */
    public function connect($cfg = null) {
        if ($cfg) $this->cfg = $cfg;
        if ($this->connection) return $this;
        $this->connection = new \mysqli(
            $this->cfg->host,
            $this->cfg->user,
            $this->cfg->password,
            $this->cfg->dbname
        );
        if (!$this->connection || $this->connection->connect_errno) {
            throw new Exception('Connection failed: ' . mysqli_connect_error());
        }
        $this->connection->set_charset('utf8');
        return $this;
    }

    /**
     * Disconnect from database
     */
    public function disconnect() {
        if ($this->connection) $this->connection->close();
    }

    /**
     * Quote database name
     * @param string $name
     * @return string
     */
    public function quoteName($name) {
        if (is_array($name) || is_object($name)) {
            $names = array_map(array($this, 'quoteName'), (array)$name);
            return implode(', ', $names);
        }
        return '`' . $name . '`';
    }

    /**
     * Quote database value
     * @param mixed $val
     * @return string
     */
    public function quote($val) {
        if ($val === null) return 'NULL';
        if ($val === false) return '0';
        if ($val === true) return '1';
        if (is_int($val)) return (string)$val;
        if (is_array($val) || is_object($val)) {
            $values = array_map(array($this, 'quote'), (array)$val);
            return implode(', ', $values);
        }
        return "'" . $this->connection->real_escape_string($val) . "'";
    }

    /**
     * Select query
     * @param string $what
     * @return \Dal\MysqlQuery
     */
    public function select($what = '*') {
        $args = func_get_args();
        $args[0] = 'SELECT ' . $what;
        return $this->queryArgs($args);
    }

    /**
     * Select all query
     * @return \Dal\MysqlQuery
     */
    public function selectFrom() {
        $args = func_get_args();
        $args[0] = 'SELECT * FROM ' . $args[0];
        return $this->queryArgs($args);
    }

    /**
     * Limit query
     * @param int $limit
     * @param int $offset
     * @return \Dal\AbstractQuery
     */
    public function limit($limit, $offset = 0) {
        if ($offset) {
            return $this->query('LIMIT ? OFFSET ?', (int)$offset, (int)$limit);
        } else {
            return $this->query('LIMIT ?', (int)$limit);
        }
    }

    /**
     * Execute query
     * @param bool $last_id
     * @return \Dal\AbstractQuery
     * @throws \Dal\Exception
     */
    public function exec($last_id = false) {
        $sql = $this->sql;
        $this->result = @$this->connection->query($sql);

        if (!$this->result && substr_count($this->connection->error, 'gone away')) {
            $this->connection = null;
            $this->connect();
            $this->exec($last_id);
        }

        $this->sql = '';
        $this->classname = null;
        if (!$this->result) {
            throw new Exception(sprintf("MySQL ERROR: %s, SQL: %s", $this->connection->error, $sql),
                $this->connection->errno);
        }
        return $last_id ? $this->lastId() : $this->result;
    }

    /**
     * Get last inserted id
     * @return mixed
     */
    public function lastId() {
        return $this->connection->insert_id;
    }

    /**
     * Get affected rows
     * @return int
     */
    public function affectedRows() {
        return $this->connection->affected_rows;
    }

    /**
     * Fetch single value from database
     * @return mixed
     */
    public function fetchCell() {
        $this->exec();
        $row = $this->result->fetch_row();
        return $row[0];
    }

    /**
     * Fetch row as object
     * @param string $class Result type
     * @return object
     */
    public function fetchObject($class = null) {
        if (!$class) $class = $this->classname;
        $this->exec();
        $row = $class ? $this->result->fetch_object($class)
            : $this->result->fetch_object();
        return $row;
    }

    /**
     * Fetch row as array
     * @return array
     */
    public function fetchArray() {
        $this->exec();
        $row = $this->result->fetch_row();
        return $row;
    }

    /**
     * Fetch row as associative array
     * @return array
     */
    public function fetchAssoc() {
        $this->exec();
        $row = $this->result->fetch_assoc();
        return $row;
    }

    /**
     * Fetch all rows as array of objects
     * @param string $class Result type
     * @return array
     */
    public function fetchAllObject($class = null) {
        if (!$class) $class = $this->classname;
        $this->exec();
        $res = [];
        while ($row = $class ? $this->result->fetch_object($class)
            : $this->result->fetch_object()) {
            $res []= $row;
        }
        return $res;
    }

    /**
     * Fetch all rows as array of arrays
     * @return array
     */
    public function fetchAllArray() {
        $this->exec();
        $res = $this->result->fetch_all(MYSQLI_NUM);
        return $res;
    }

    /**
     * Fetch all rows as array of associative arrays
     * @return array
     */
    public function fetchAllAssoc() {
        $this->exec();
        $res = $this->result->fetch_all(MYSQLI_ASSOC);
        return $res;
    }

    /**
     * Fetch first result field from all rows as array
     * @param int|string $column Column index or name
     * @return array
     */
    public function fetchColumn($column = 0) {
        return array_map(function ($row) use ($column) {
            return $row[$column];
        }, is_int($column) ? $this->fetchAllArray() : $this->fetchAllAssoc());
    }

    // Useless methods

    /**
     * Fetch row as object from database
     * @param string $table
     * @param string $field Field in where clause
     * @param string $value Value in where clause
     * @param string $class Result type
     * @return object
     */
    public function getObject($table, $field, $value, $class = null) {
        $this->select()->from($table)->where("$field = ?", $value);
        return $this->fetchObject($class);
    }

    /**
     * Fetch row as array from database
     * @param string $table
     * @param string $field Field in where clause
     * @param string $value Value in where clause
     * @return array
     */
    public function getArray($table, $field, $value) {
        $this->select()->from($table)->where("$field = ?", $value);
        return $this->fetchArray();
    }

    /**
     * Fetch row as associative array from database
     * @param string $table
     * @param string $field Field in where clause
     * @param string $value Value in where clause
     * @return array
     */
    public function getAssoc($table, $field, $value) {
        $this->select()->from($table)->where("$field = ?", $value);
        return $this->fetchAssoc();
    }

}
