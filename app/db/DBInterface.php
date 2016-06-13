<?php

/**
 * Database methods definitions.
 *
 * @copyright 2016 onwards Julen Pardo & Mondragon Unibertsitatea
 * @license   Apache 2.0 http://www.apache.org/licenses/LICENSE-2.0
 */

namespace DBConnectionWatcher;

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
     * @return The number of connections.
     */
    public function queryConnectionNumber();
}
