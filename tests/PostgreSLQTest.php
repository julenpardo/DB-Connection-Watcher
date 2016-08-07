<?php

require_once(dirname(__FILE__) . '/../vendor/autoload.php');
require_once(dirname(__FILE__) . '/../src/db/ConnectionException.php');
require_once(dirname(__FILE__) . '/../src/db/PreparedStatementCreationException.php');
require_once(dirname(__FILE__) . '/../src/db/DBInterface.php');
require_once(dirname(__FILE__) . '/../src/db/dbms/PostgreSQL.php');

use DBConnectionWatcher\DB\DBMS\PostgreSQL;

class PostgreSQLTest extends PHPUnit_Framework_TestCase
{
    public function testConnect()
    {
        $database = 'postgres';
        $username = 'postgres';
        $password = 'postgres';
        $host = 'localhost';
        $port = 5433;

        $postgresql = new PostgreSQL($database, $username, $password, $host, $port);

        try {
            $postgresql->connect();
        } catch (Exception $exception) {
            $this->fail("No exception should be thrown.");
        }

        $postgresql->disconnect();
    }

    /**
     * @expectedException \DBConnectionWatcher\DB\ConnectionException
     */
    public function testConnectException()
    {
        $database = 'non_existing_database';
        $username = 'postgres';
        $password = 'postgres';
        $host = 'localhost';
        $port = 5433;

        $postgresql = new PostgreSQL($database, $username, $password, $host, $port);
        $postgresql->connect();
    }

    public function testDisconnect()
    {
        $database = 'postgres';
        $username = 'postgres';
        $password = 'postgres';
        $host = 'localhost';
        $port = 5433;

        $postgresql = new PostgreSQL($database, $username, $password, $host, $port);

        try {
            $postgresql->connect();
            $postgresql->disconnect();
        } catch (Exception $exception) {
            $this->fail("No exception should be thrown.");
        }
    }

    /**
     * @expectedException \DBConnectionWatcher\DB\ConnectionException
     */
    public function testDisconnectException()
    {
        $postgresql = new PostgreSQL(null, null, null);
        $postgresql->disconnect();
    }

    public function testQueryConnectionNumber()
    {
        $expectedConnections = 5;

        $database = 'postgres';
        $username = 'postgres';
        $password = 'postgres';
        $host = 'localhost';
        $port = 5433;

        $postgresql = new PostgreSQL($database, $username, $password, $host, $port);
        $postgresql->connect();

        $connections = [];

        // The connection must be created with PGSQL_CONNECTION_FORCE_NEW flag because, according to the
        // documentation, "if a second call is made to pg_pconnect() with the same connection_string as
        // an existing connection, the existing connection will be returned unless you pass
        // PGSQL_CONNECT_FORCE_NEW as connect_type."
        for ($index = 0; $index < $expectedConnections; $index++) {
            $connection = pg_connect(
                "host=$host port=$port dbname=$database user=$username password=$password",
                PGSQL_CONNECT_FORCE_NEW
            );

            array_push($connections, $connection);
        }

        $actualConnections = $postgresql->queryConnectionNumber();

        $this->assertEquals($expectedConnections, $actualConnections);

        foreach ($connections as $connection) {
            pg_close($connection);
        }
    }

    public function testQueryConnectionNumberZero()
    {
        $database = 'postgres';
        $username = 'postgres';
        $password = 'postgres';
        $host = 'localhost';
        $port = 5433;

        $postgresql = new PostgreSQL($database, $username, $password, $host, $port);
        $postgresql->connect();

        $expectedConnections = 0;
        $actualConnections = $postgresql->queryConnectionNumber();

        $this->assertEquals($expectedConnections, $actualConnections);
    }

    /**
     * @expectedException \DBConnectionWatcher\DB\PreparedStatementCreationException
     */
    public function testQueryConnectionNumberException()
    {
        $postgresql = new PostgreSQL(null, null, null);
        $postgresql->queryConnectionNumber();
    }
}