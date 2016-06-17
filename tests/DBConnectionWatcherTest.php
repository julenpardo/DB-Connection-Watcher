<?php

require_once(dirname(__FILE__) . '/../src/db/DBFactory.php');
require_once(dirname(__FILE__) . '/../src/db/DBInterface.php');
require_once(dirname(__FILE__) . '/../src/db/ConnectionException.php');
require_once(dirname(__FILE__) . '/../src/db/PreparedStatementCreationException.php');
require_once(dirname(__FILE__) . '/../src/db/dbms/PostgreSQL.php');
require_once(dirname(__FILE__) . '/../src/configuration/ConfigurationException.php');
require_once(dirname(__FILE__) . '/../src/mailer/MailSendException.php');
require_once(dirname(__FILE__) . '/../src/mailer/Mailer.php');
require_once(dirname(__FILE__) . '/../src/DBConnectionWatcher.php');

use DBConnectionWatcher\DBConnectionWatcher;
use DBConnectionWatcher\DB\DBMS\PostgreSQL;

class DBConnectionWatcherTest extends PHPUnit_Framework_Testcase
{
    protected $dbConnectionWatcher;

    protected function setUp()
    {
        $this->dbConnectionWatcher = new DBConnectionWatcher();
    }

    protected function getMethod($name)
    {
        $class = new ReflectionClass('DBConnectionWatcher\DBConnectionWatcher');
        $method = $class->getMethod($name);
        $method->setAccessible(true);

        return $method;
    }

    protected function createDatabaseConnections($database, $username, $password, $host, $port, $number)
    {
        $connections = [];

        for ($index = 0; $index < $number; $index++) {
            $connection = pg_connect(
                "host=$host port=$port dbname=$database user=$username password=$password",
                PGSQL_CONNECT_FORCE_NEW
            );

            array_push($connections, $connection);
        }

        return $connections;
    }

    protected function closeConnections($connections)
    {
        foreach ($connections as $connection) {
            pg_close($connection);
        }
    }

    public function testCheckStatusBelowThreshold()
    {
        $database = 'postgres';
        $username = 'postgres';
        $password = 'postgres';
        $host     = 'localhost';
        $port     = 5433;

        $db = new PostgreSQL($database, $username, $password, $host, $port);
        $email = 'julen.pardo@outlook.es';
        $connectionThreshold = 5;

        // The key part of the test is to assert that 'sendThresholdExceededMail' method is not called.
        $mailer = $this->getMock('DBConnectionWatcher\Mailer\Mailer');
        $mailer->expects($this->never())
            ->method('sendThresholdExceededMail');

        $this->dbConnectionWatcher->setMailer($mailer);

        $connections = $this->createDatabaseConnections($database, $username, $password, $host, $port,
            $connectionThreshold - 1);

        $method = $this->getMethod('checkStatus');
        $method->invokeArgs($this->dbConnectionWatcher, [$db, $email, $connectionThreshold]);

        $this->closeConnections($connections);
    }
}