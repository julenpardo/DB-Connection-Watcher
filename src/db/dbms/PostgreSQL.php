<?php

/**
 * Specific DBInterface method implementations for PostgreSQL.
 *
 * @author Julen Pardo
 */

namespace DBConnectionWatcher\DB\DBMS;

use DBConnectionWatcher\DB\ConnectionException;
use DBConnectionWatcher\DB\DBInterface;
use DBConnectionWatcher\DB\PreparedStatementCreationException;

/**
 * Class PostgreSQL with specific method implementations.
 *
 * @package DBConnectionWatcher\DB\DBMS
 * @author  Julen Pardo
 */
class PostgreSQL implements DBInterface
{
    /**
     * The name of the prepared statement.
     * @const
     */
    const CONNECTION_NUMBER_STATEMENT = 'connection_number';

    /**
     * The database connection resource.
     * @var
     */
    private $connection;

    /**
     * Database name.
     * @var string
     */
    private $database;

    /**
     * Username to connect to database.
     * @var string
     */
    private $username;

    /**
     * Password for the username to connect to database.
     * @var string
     */
    private $password;

    /**
     * The host where the database is.
     * @var string
     */
    private $host;

    /**
     * The port the database service is listening to.
     * @var int
     */
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
     * Gets the database.
     *
     * @return string Database name.
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * Gets the host.
     *
     * @return string Host name.
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Creates the connection to the database.
     *
     * @throws \Exception If an error occurs when connecting to database, or creating the prepared statement.
     */
    public function connect()
    {
        $connectionString = "host=$this->host port=$this->port dbname=$this->database "
            . "user=$this->username password=$this->password";

        $this->connection = @pg_connect($connectionString, PGSQL_CONNECT_FORCE_NEW);

        if (!$this->connection) {
            throw new ConnectionException('connect');
        }
    }

    /**
     * Closes the established connection to the database.
     *
     * @throws \Exception If an error occurs when closing database connection.
     */
    public function disconnect()
    {
        $connectionClosed = @pg_close($this->connection);

        if (!$connectionClosed) {
            throw new ConnectionException('close');
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
     * @throws \DBConnectionWatcher\DB\PreparedStatementCreationException If an error occurs creating the prepared
     * statement.
     * @return int The number of connections.
     */
    public function queryConnectionNumber()
    {
        $connectionNumberSql = 'SELECT COUNT(activity.datid) '
            . 'FROM pg_stat_activity activity '
            . "WHERE datname = $1 "
            . 'GROUP BY activity.datid';

        $prepared = @pg_prepare($this->connection, self::CONNECTION_NUMBER_STATEMENT, $connectionNumberSql);

        if (!$prepared) {
            throw new PreparedStatementCreationException();
        }

        $queryResult = pg_execute($this->connection, self::CONNECTION_NUMBER_STATEMENT, array($this->database));
        $row = pg_fetch_row($queryResult);

        if (!$row) {
            $connectionNumber = 0;
        } else {
            $connectionNumber = $row[0] - 1;
        }

        return $connectionNumber;
    }
}
