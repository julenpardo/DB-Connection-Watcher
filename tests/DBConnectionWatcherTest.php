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

class DBConnectionWatcherTest extends \PHPUnit_Framework_Testcase
{
    protected $dbConnectionWatcher;
    protected $configurationFile = DEFAULT_CONFIG_PATH;

    protected function setUp()
    {
        $this->dbConnectionWatcher = new DBConnectionWatcher();
    }

    protected function tearDown()
    {
        $this->deleteConfigFileIfExists();
    }

    protected function getMethod($name)
    {
        $class = new \ReflectionClass('DBConnectionWatcher\DBConnectionWatcher');
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

    protected function deleteConfigFileIfExists()
    {
        if (file_exists($this->configurationFile)) {
            unlink($this->configurationFile);
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

    public function testCheckStatusEqualThreshold()
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
            $connectionThreshold);

        $method = $this->getMethod('checkStatus');
        $method->invokeArgs($this->dbConnectionWatcher, [$db, $email, $connectionThreshold]);

        $this->closeConnections($connections);
    }

    public function testCheckStatusAboveThreshold()
    {
        $database = 'postgres';
        $username = 'postgres';
        $password = 'postgres';
        $host     = 'localhost';
        $port     = 5433;

        $db = new PostgreSQL($database, $username, $password, $host, $port);
        $email = 'julen.pardo@outlook.es';
        $connectionThreshold = 5;

        // The key part of the test is to assert that 'sendThresholdExceededMail' method IS called.
        $mailer = $this->getMock('DBConnectionWatcher\Mailer\Mailer');
        $mailer->expects($this->once())
            ->method('sendThresholdExceededMail');

        $this->dbConnectionWatcher->setMailer($mailer);

        $connections = $this->createDatabaseConnections($database, $username, $password, $host, $port,
            $connectionThreshold + 1);

        $method = $this->getMethod('checkStatus');
        $method->invokeArgs($this->dbConnectionWatcher, [$db, $email, $connectionThreshold]);

        $this->closeConnections($connections);
    }

    /**
     * @expectedException \DBConnectionWatcher\DB\ConnectionException
     */
    public function testCheckStatusConnectionException()
    {
        $database = 'non_existing_database';
        $username = 'postgres';
        $password = 'postgres';
        $host     = 'localhost';
        $port     = 5433;

        $db = new PostgreSQL($database, $username, $password, $host, $port);
        $email = 'julen.pardo@outlook.es';
        $connectionThreshold = 5;

        $method = $this->getMethod('checkStatus');
        $method->invokeArgs($this->dbConnectionWatcher, [$db, $email, $connectionThreshold]);
    }

    /**
     * @expectedException \DBConnectionWatcher\Mailer\MailSendException
     */
    public function testCheckStatusMailSendException()
    {
        $database = 'postgres';
        $username = 'postgres';
        $password = 'postgres';
        $host     = 'localhost';
        $port     = 5433;

        $db = new PostgreSQL($database, $username, $password, $host, $port);
        $email = 'julen.pardo@outlook.es';
        $connectionThreshold = 5;

        $mailer = $this->getMock('DBConnectionWatcher\Mailer\Mailer');
        $mailer->expects($this->once())
               ->method('sendThresholdExceededMail')
               ->will($this->throwException(new \DBConnectionWatcher\Mailer\MailSendException()));

        $this->dbConnectionWatcher->setMailer($mailer);

        $connections = $this->createDatabaseConnections($database, $username, $password, $host, $port,
            $connectionThreshold + 1);

        $method = $this->getMethod('checkStatus');
        $method->invokeArgs($this->dbConnectionWatcher, [$db, $email, $connectionThreshold]);

        $this->closeConnections($connections);
        $db->disconnect();
    }

    public function testCheckStatus()
    {
        $this->markTestSkipped('This cannot be still tested because the mail() function cannot be mocked.');
        $database = 'postgres';
        $username = 'postgres';
        $password = 'postgres';
        $host     = 'localhost';
        $port     = 5433;

        $db = new PostgreSQL($database, $username, $password, $host, $port);
        $email = 'julen.pardo@outlook.es';
        $connectionThreshold = 5;

        $connections = $this->createDatabaseConnections($database, $username, $password, $host, $port,
            $connectionThreshold + 1);

        $method = $this->getMethod('checkStatus');

        try {
            $method->invokeArgs($this->dbConnectionWatcher, [$db, $email, $connectionThreshold]);
        } catch (\Exception $exception) {

            $this->fail('No exception should be thrown: ' . $exception->getMessage());
        }
    }

    public function testRunConfigurationExceptionCode()
    {
        $expected = DBConnectionWatcher::ERROR_CONFIGURATION_EXCEPTION;
        $actual = $this->dbConnectionWatcher->run();

        $this->assertEquals($expected, $actual);
    }

    public function testRunConnectionExceptionCode()
    {
        $expected = DBConnectionWatcher::ERROR_CONNECTION_EXCEPTION;
        $configuration = '[section 1]
            database = postgres
            username = non_existing_user
            password = postgres
            host = localhost
            port = 5433
            email = julen.pardo@outlook.es
            connection_threshold = 10
            dbms = postgresql
        ';

        file_put_contents($this->configurationFile, $configuration);

        $actual = $this->dbConnectionWatcher->run();

        $this->assertEquals($expected, $actual);
    }

    public function testRunMailSendExceptionCode()
    {
        $expected = DBConnectionWatcher::ERROR_MAIL_SEND_EXCEPTION;
        $connectionThreshold = 2;
        $database = 'postgres';
        $username = 'postgres';
        $password = 'postgres';
        $host     = 'localhost';
        $port     = 5433;

        $configuration = "[section 1]
            database = $database
            username = $username
            password = $password
            host = $host
            port = $port
            email = julen.pardo@outlook.es
            connection_threshold = $connectionThreshold
            dbms = postgresql
        ";

        file_put_contents($this->configurationFile, $configuration);

        $mailer = $this->getMock('DBConnectionWatcher\Mailer\Mailer');
        $mailer->expects($this->once())
            ->method('sendThresholdExceededMail')
            ->will($this->throwException(new \DBConnectionWatcher\Mailer\MailSendException()));

        $this->dbConnectionWatcher->setMailer($mailer);

        $connections = $this->createDatabaseConnections($database, $username, $password, $host, $port,
            $connectionThreshold + 1);

        $actual = $this->dbConnectionWatcher->run();

        $this->assertEquals($expected, $actual);

        $this->closeConnections($connections);
    }

    public function testRun()
    {
        $expected = DBConnectionWatcher::SUCCESS;
        $configuration = '[section 1]
            database = postgres
            username = postgres
            password = postgres
            host = localhost
            port = 5433
            email = julen.pardo@outlook.es
            connection_threshold = 10
            dbms = postgresql
        ';

        file_put_contents($this->configurationFile, $configuration);

        $actual = $this->dbConnectionWatcher->run();

        $this->assertEquals($expected, $actual);
    }
}