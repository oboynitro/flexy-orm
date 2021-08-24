<?php


namespace Oboynitro\FlezyORM;


use PDO;
use PDOException;

class FlezyORM
{
    /** @var PDO database connection object */
    private static PDO $connection;


    /** @var string table name */
    private string $table;


    /** @var object data object returned from queries */
    private object $data;


    /** @var string selection string for queries */
    private string $selectClause;


    /** @var string condition string for queries */
    private string $whereClause;


    /** @var string order string for queries */
    private string $orderClause;


    /** @var string limit string for queries */
    private string $limitClause;


    /**
     * Connect to a database
     *
     * @access public
     * @param $host
     * @param $username
     * @param $password
     * @param $database
     * @param array $options
     * @param int $port
     * @return FlezyORM
     */
    public function createConnection(
        $host, $username, $password, $database,
        array $options = [], int $port = 3306): FlezyORM
    {
        try {
            $dsn = "mysql:dbname=$database;host=$host;port=$port";
            static::$connection = new PDO($dsn, $username, $password, $options);
            if (empty($options)) {
                static::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                static::$connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
            }
            return $this;
        } catch (PDOException $e) {
            die(__LINE__ . " " . $e->getMessage());
        }
    }


    /**
     * Pass an existing PDO connection object to Flezy
     *
     * @access public
     * @param PDO $connection PDO connection instance.
     * @return FlezyORM
     */
    public function useConnection(PDO $connection): FlezyORM
    {
        static::$connection = $connection;
        return $this;
    }


    /**
     * Get our connection instance.
     *
     * @access public
     * @return PDO
     */
    public static function getConnection(): PDO
    {
        return static::$connection;
    }


    /**
     * Select the table to use for queries
     *
     * @access public
     * @param string $table
     * @return FlezyORM
     */
    public function table(string $table): FlezyORM
    {
        $this->table = $table;
        return $this;
    }


    /**TODO: extend to build all queries (create, update, delete)
     * Builds database selection query
     *
     * @access public
     * @return string
     */
    public function buildQuery(): string
    {
        if (!empty($this->table)) {
            $this->selectClause = !empty($this->selectClause) ? $this->selectClause : "*";
            $this->whereClause = !empty($this->whereClause) ? $this->whereClause : "";
            $this->orderClause = !empty($this->orderClause) ? $this->orderClause : "";
            $this->limitClause = !empty($this->limitClause) ? $this->limitClause : "";
            return /** @lang text */ "
            SELECT $this->selectClause FROM $this->table 
            $this->whereClause 
            $this->orderClause 
            $this->limitClause";
        } else {
            die("No database table specified");
        }
    }


    /**
     * Fetch all data (multiple)
     *
     * @access public
     * @return object
     */
    public function all(): object
    {
        $this->selectClause = "*";
        $query = $this->buildQuery();
        try {
            $stmt = static::$connection->prepare($query);
            $stmt->execute();
            $this->data = $stmt->rowCount() ? $stmt->fetchObject() : [];
            return $this->data;
        }catch (PDOException $e) {
            die("Internal Server Error, please contract the site administrator or try again");
        }
    }


    /**
     * Selects only provided columns from table
     *
     * @access public
     * @param string $fields comma separate for multiple fields
     * @return FlezyORM
     */
    public function select(string $fields): FlezyORM
    {
        $this->selectClause = $fields;
        $this->buildQuery();
        return $this;
    }


    /**
     * Counts all rows in table
     *
     * @access public
     * @return object
     */
    public function count(): object
    {
        $countDataName = $this->table . '_count';
        $this->selectClause = "COUNT(*) as $countDataName";
        $query = $this->buildQuery();
        try {
            $stmt = static::$connection->prepare($query);
            $stmt->execute();
            $this->data = $stmt->rowCount() ? $stmt->fetch() : [];
            return $this->data;
        }catch (PDOException $e) {
            die("Internal Server Error, please contract the site administrator or try again");
        }
    }


    /**
     * Fetch all data (multiple)
     *
     * @access public
     * @return object
     */
    public function get(): object
    {
        $query = $this->buildQuery();
        try {
            $stmt = static::$connection->prepare($query);
            $stmt->execute();
            $this->data = $stmt->rowCount() ? $stmt->fetch() : [];
            return $this->data;
        }catch (PDOException $e) {
            die("Internal Server Error, please contract the site administrator or try again");
        }
    }


    /**
     * Order fetch results by provided field in Ascending mode
     *
     * @access public
     * @param string $field comma separate for multiple fields
     * @return FlezyORM
     */
    public function orderBy(string $field): FlezyORM
    {
        $this->orderClause = "ORDER BY $field ASC";
        $this->buildQuery();
        return $this;
    }


    /**
     * Order fetch results by provided field in Descending mode
     *
     * @access public
     * @param string $field comma separate for multiple fields
     * @return FlezyORM
     */
    public function orderByDesc(string $field): FlezyORM
    {
        $this->orderClause = "ORDER BY $field DESC";
        $this->buildQuery();
        return $this;
    }


    /**
     * Specify number of result returned from query (limit)
     *
     * @access public
     * @param int $resultCount
     * @return FlezyORM
     */
    public function limit(int $resultCount): FlezyORM
    {
        $this->limitClause = "LIMIT $resultCount";
        $this->buildQuery();
        return $this;
    }


    /**
     * Select data by providing the primary key (defaults to 'id' column)
     *
     * @access public
     * @param int $primary_key defaults to 'id' column
     * @return object
     */
    public function find(int $primary_key): object
    {
        $this->whereClause = "WHERE id = '$primary_key'";
        $query = $this->buildQuery();
        try {
            $stmt = static::$connection->prepare($query);
            $stmt->execute();
            $this->data = $stmt->rowCount() ? $stmt->fetch() : [];
            return $this->data;
        }catch (PDOException $e) {
            die("Internal Server Error, please contract the site administrator or try again");
        }
    }


    /**
     * Filter fetch results based on field and value and optional operator
     *
     * @access public
     * @param string $field
     * @param string $value
     * @param string|null $operator optional, defaults to '=' if not provided
     * @return FlezyORM
     */
    public function where(string $field, string $value, ?string $operator = "="): FlezyORM
    {
        $this->whereClause = "WHERE $field $operator '$value'";
        $this->buildQuery();
        return $this;
    }


    /**
     * Filter fetch results based on field and value (use for searching)
     *
     * @access public
     * @param string $field
     * @param string $value
     * @return FlezyORM
     */
    public function whereLike(string $field, string $value): FlezyORM
    {
        $this->whereClause = "WHERE $field LIKE '%$value%'";
        $this->buildQuery();
        return $this;
    }


    /**
     * Inserts new row into database table given
     *
     * @access public
     * @param array $data
     * @return bool|null
     */
    public function create(array $data): bool
    {
        if (!empty($this->table)) {
            $normalized = [];
            foreach (array_values(array_keys($data)) as $_) $normalized[] = "?";

            $field_names = implode(", ", array_keys($data));
            $bind_values = implode(", ", array_values($normalized));

            try {
                $query = "/** @lang text */ INSERT INTO $this->table ($field_names) VALUES ($bind_values)";
                $stmt = static::$connection->prepare($query);
                $values = [];
                foreach (array_values($data) as $val) {
                    $values[] = $val;
                }
                if ($stmt->execute(array_values($values))) return true;
            } catch (PDOException $e) {
                die("Internal Server Error, please contract the site administrator or try again");
            }
        } else {
            die("No database table specified");
        }
    }


    /**
     * Updates an existing row in the database table given
     *
     * @access public
     * @param string $primary_key
     * @param array $data
     * @return bool|null
     */
    public function update(string $primary_key, array $data): bool
    {
        if (!empty($this->table)){
            $normalized = "";

            foreach (array_keys($data) as $val)
                $normalized .= "$val=?, ";
            $bind_values = rtrim($normalized, ", ");

            try {
                $query = "/** @lang text */ UPDATE $this->table SET $bind_values WHERE id=?";
                $stmt = self::$connection->prepare($query);
                $values = [];
                foreach (array_values($data) as $val) {
                    $values[] = $val;
                }
                $values[] = $primary_key;
                if ($stmt->execute(array_values($values))) return true;
            } catch (PDOException $e) {
                die("Internal Server Error, please contract the site administrator or try again \n$e");
            }
        }
        else {
            die("No database table specified");
        }
    }


    /**
     * Deletes a row in the database table given
     *
     * @access public
     * @param string $primary_key
     * @return bool|null
     */
    public function destroy(string $primary_key): bool
    {
        if (!empty($this->table)){
            try {
                $query = /** @lang text */ "DELETE FROM $this->table WHERE id=?";
                $stmt = self::$connection->prepare($query);
                return $stmt->execute([$primary_key]);
            } catch (PDOException $e) {
                die("Internal Server Error, please contract the site administrator or try again");
            }
        }else {
            die("No database table specified");
        }
    }
}