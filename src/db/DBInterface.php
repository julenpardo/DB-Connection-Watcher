<?php

/**
 * Database methods definitions.
 *
 * @author Julen Pardo
 */

namespace DBConnectionWatcher\DB;

/**
 * Interface DBInterface.
 *
 * @package DBConnectionWatcher\DB
 * @author  Julen Pardo
 */
interface DBInterface
{
    /**
     * DBInterface constructor.
     *
     * @param string $database The database to watch.
     * @param string $username The user name to connect to the database.
     * @param string $password The password for the user name.
     * @param string $host The host where the database is. The default value is 'localhost'.
     * @param int $port The port number of the service. The default value is 5432.
     */
    public function __construct($database, $username, $password, $host = 'localhost', $port = 5432);

    /**
     * Gets the database.
     *
     * @return string Database name.
     */
    public function getDatabase();

    /**
     * Gets the host.
     *
     * @return string Host name.
     */
    public function getHost();

    /**
     * Creates the connection to the database.
     */
    public function connect();

    /**
     * Closes the established connection to the database.
     */
    public function disconnect();

    /**
     * Queries the number of current connections to the database for which the connection has been established.
     *
     * @return int The number of connections.
     */
    public function queryConnectionNumber();
}
