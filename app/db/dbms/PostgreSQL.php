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
     * any connection. Which is certainly impossible since this tool is connected to the database to make the query.
     *
     * As the connection query will also count the connection made by this tool to make that query, and that this
     * connection can be considered as "residual", it is subtracted from the connection count. For example, if the tool
     * is configured for a threshold of 1 connection (which would be weird), the tool would always return 1 if its
     * connection is not subtracted (which would be even more weird, since the database is not having a real usage).
     *
     * @return The number of connections.
     */
    public function queryConnectionNumber()
    {
        $queryResult = pg_execute($this->connection, self::CONNECTION_NUMBER_STATEMENT, []);
        $row = pg_fetch_row($queryResult);

        if (!$row) {
            $connectionNumber = 0;
        } else {
            $connectionNumber = $row[0] - 1;
        }

        return $connectionNumber;
    }
}
