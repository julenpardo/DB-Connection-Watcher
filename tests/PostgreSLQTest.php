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
        $port = 5432;

        $postgresql = new PostgreSQL($database, $username, $password, $host, $port);

        try {
            $postgresql->connect();
        } catch (Exception $exception) {
            $this->fails("No exception should be thrown.");
        }
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
        $port = 5432;

        $postgresql = new PostgreSQL($database, $username, $password, $host, $port);
        $postgresql->connect();
    }

}