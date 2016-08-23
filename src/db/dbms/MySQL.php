<?php

/**
 * Specific DBInterface method implementations for MySQL.
 *
 * @author Julen Pardo
 */

namespace DBConnectionWatcher\DB\DBMS;

use DBConnectionWatcher\DB\ConnectionException;
use DBConnectionWatcher\DB\DBInterface;
use DBConnectionWatcher\DB\PreparedStatementCreationException;

/**
 * Class MySQL with specific method implementations.
 *
 * @package DBConnectionWatcher\DB\DBMS
 * @author  Julen Pardo
 */
class MySQL implements DBInterface
{
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
     * MySQL constructor.
     *
     * @param string $database The database to watch.
     * @param string $username The user name to connect to the database.
     * @param string $password The password for the user name.
     * @param string $host The host where the database is. The default value is 'localhost'.
     * @param int $port The port number of the service. The default value is 5432.
     */
    public function __construct($database, $username, $password, $host = 'localhost', $port = 3306)
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
        $this->connection = @mysqli_connect(
            $this->host,
            $this->username,
            $this->password,
            $this->database,
            $this->port
        );

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
        $connectionClosed = @mysqli_close($this->connection);

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
        $connectionNumberSql = 'SELECT COUNT(processlist.id) '
                    . 'FROM INFORMATION_SCHEMA.PROCESSLIST processlist '
                    . 'WHERE processlist.db = ?';

        $statement = $connection->prepare($connectionNumberSql);

        $statement->bind_param("s", $this->database);

        $statement->execute();
        $statement->bind_result($connectionNumber);
        $statement->fetch();

        if (!$connectionNumber) {
            $connectionNumber = 0;
        } else {
            $connectionNumber -= 1;
        }

        $statement->close();

        return $connectionNumber;
    }
}
