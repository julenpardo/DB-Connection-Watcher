<?php

require_once('app/db/dbms/PostgreSQL.php');

use DBConnectionWatcher\DBMS\PostgreSQL;

class PostgreSQLTest extends PHPUnit_Framework_TestCase
{
    public function testConnectException()
    {
        $database = 'non_existing_database';
        $username = 'username';
        $password = 'password';
        $host = 'localhost';
        $port = 5433;

        try {
            new PostgreSQL($database, $username, $password, $host, $port);
        } catch (Exception $exception) {
            $this->fail("No exception should be thrown.");
        }
    }

}