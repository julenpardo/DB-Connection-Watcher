<?php

require_once(dirname(__FILE__) . '/../src/db/ConnectionException.php');
require_once(dirname(__FILE__) . '/../src/db/PreparedStatementCreationException.php');
require_once(dirname(__FILE__) . '/../src/db/DBInterface.php');
require_once(dirname(__FILE__) . '/../src/db/dbms/MySQL.php');

use DBConnectionWatcher\DB\DBMS\MySQL;

class MySQLTest extends PHPUnit_Framework_TestCase
{
    public function testConnect()
    {
        $database = 'mysql';
        $username = 'root';
        $password = 'root';
        $host     = '127.0.0.1';
        $port     = 3307;

        $mysql = new MySQL($database, $username, $password, $host, $port);

        try {
            $mysql->connect();
        } catch (Exception $exception) {
            $this->fail('No exception should be thrown: ' . $exception->getMessage());
        }

        $mysql->disconnect();
    }

    /**
     * @expectedException \DBConnectionWatcher\DB\ConnectionException
     */
    public function testConnectException()
    {
        $database = 'non_existing_database';
        $username = 'root';
        $password = 'root';
        $host     = '127.0.0.1';
        $port     = 3307;

        $mysql = new MySQL($database, $username, $password, $host, $port);
        $mysql->connect();
    }
}
