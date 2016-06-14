<?php

namespace DBConnectionWatcher\DB\DBMS;

use DBConnectionWatcher\DB\DBInterface;

class PostgreSQL implements DBInterface
{
    const CONNECTION_NUMBER_STATEMENT = 'connection_number';

    private $connection;
    private $connectionNumberStatement;
    private $database;
    private $username;
    private $password;
    private $host;
    private $port;

    /**
     * PostgreSQL constructor.
     *
     * @param string $database The database to watch.
     * @param string $username The user name to connect to the database.
     * @param string $password The password for the user name.
     * @param string $host The host where the database is. The default value is 'localhost'.
     * @param int $port The port number of the service. The default value is 5432.
     */
    public function __construct($database, $username, $password, $host = 'localhost', $port = 5432)
    {
        $this->database = $database;
        $this->username = $username;
        $this->password = $password;
        $this->host = $host;
        $this->port = $port;
    }

    /**
     * Creates the connection to the database.
     *
     * @throws \Exception If an error occurs when connecting to database.
     */
    public function connect()
    {
        $connectionString = "host=$this->host port=$this->port dbname=$this->database "
            . "user=$this->username password=$this->password";

        $this->connection = pg_connect($connectionString);

        if (!$this->connection) {
            throw new \Exception('An error occurred when trying to connect to PostgreSQL database: '
                . pg_last_error($this->connection));
        }

        $this->createConnectionNumberStatement();
    }

    /**
     * Creates the prepared statement for the connection number. Using a prepared statement is more optimal that
     * executing 'non-prepared' queries.
     */
    protected function createConnectionNumberStatement()
    {
        $connectionNumberSql = 'SELECT COUNT(activity.datid) '
            . 'FROM pg_stat_activity activity '
            . "WHERE datname = '$this->database' "
            . 'GROUP BY activity.datid';

        $this->connectionNumberStatement = pg_prepare(
            $this->connection,
            self::CONNECTION_NUMBER_STATEMENT,
            $connectionNumberSql
        );
    }

    /**
     * Closes the established connection to the database.
     *
     * @throws \Exception If an error occurs when closing database connection.
     */
    public function disconnect()
    {
        $connectionClosed = pg_close($this->connection);

        if (!$connectionClosed) {
            throw new \Exception('An error occurred when closing the PostgreSQL database connection: '
                . pg_last_error($this->connection));
        }
    }

    /**
     * Queries the number of current connections to the database for which the connection has been established.
     * If the fetched row is false, means that the query has returned no row, so, that means that the database has not
     * any connection.
     *
     * @return The number of connections.
     */
    public function queryConnectionNumber()
    {
        $queryResult = pg_execute($this->connection, self::CONNECTION_NUMBER_STATEMENT, []);
        $row = pg_fetch_row($queryResult);

        $connectionNumber = (!$row) ? 0 : $row[0];

        return $connectionNumber;
    }
}
