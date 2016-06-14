<?php

require_once(dirname(__FILE__) . '/../app/db/DBInterface.php');
require_once(dirname(__FILE__) . '/../app/db/dbms/PostgreSQL.php');

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
     * @expectedException Exception
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
     * @expectedException Exception
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

        for ($index = 0; $index < $expectedConnections; $index++) {
            $connection = pg_connect("host=$host port=$port dbname=$database user=$username password=$password");

            array_push($connections, $connection);
        }

        $actualConnections = $postgresql->queryConnectionNumber();

        $this->assertEquals($expectedConnections, $actualConnections);

        foreach ($connections as $connection) {
            pg_close($connection);
        }
    }
}