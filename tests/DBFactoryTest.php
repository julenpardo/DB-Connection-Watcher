<?php

require_once(dirname(__FILE__) . '/../src/db/DBFactory.php');
require_once(dirname(__FILE__) . '/../src/db/DBInterface.php');
require_once(dirname(__FILE__) . '/../src/db/dbms/PostgreSQL.php');
require_once(dirname(__FILE__) . '/../src/configuration/ConfigurationException.php');

use DBConnectionWatcher\DB\DBFactory;
use DBConnectionWatcher\DB\DBMS\PostgreSQL;

class DBFactoryTest extends PHPUnit_Framework_Testcase
{
    /**
     * @expectedException \DBConnectionWatcher\Configuration\ConfigurationException
     */
    public function testGetInstanceConfigurationException()
    {
        $configuration = [
            'database' => 'testdb',
            'username' => 'postgres',
            'password' => 'postgres',
            'host' => 'localhost',
            'port' => '5433',
            'dbms' => 'non existing dbms'
        ];

        DBFactory::getInstance($configuration);
    }

    public function testGetInstance()
    {
        $configuration = [
            'database' => 'testdb',
            'username' => 'postgres',
            'password' => 'postgres',
            'host' => 'localhost',
            'port' => '5433',
            'dbms' => 'postgresql'
        ];

        try {
            $instance = DBFactory::getInstance($configuration);

            $postgresqlInstance = $instance instanceof PostgreSQL;
            $this->assertTrue($postgresqlInstance);
        } catch (\DBConnectionWatcher\Configuration\ConfigurationException $exception) {
            $this->fail('No exception should be thrown: ' . $exception->getMessage());
        }
    }
}