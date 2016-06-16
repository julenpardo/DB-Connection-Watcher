<?php

require_once(dirname(__FILE__) . '/../app/configuration/FileNotFoundException.php');
require_once(dirname(__FILE__) . '/../app/configuration/InvalidConfigurationFormatException.php');
require_once(dirname(__FILE__) . '/../app/configuration/InvalidConfigurationPropertyException.php');
require_once(dirname(__FILE__) . '/../app/configuration/InvalidConfigurationValueException.php');
require_once(dirname(__FILE__) . '/../app/configuration/InvalidConfigurationValueTypeException.php');
require_once(dirname(__FILE__) . '/../app/configuration/MissingOrExtraConfigurationsException.php');
require_once(dirname(__FILE__) . '/../app/configuration/Reader.php');

use DBConnectionWatcher\Configuration\Reader;

class ReaderTest extends PHPUnit_Framework_TestCase
{
    protected $reader;
    protected $configurationFile = 'configurationTest.ini';

    protected function setUp()
    {
        $this->reader = new Reader();
    }

    protected function tearDown()
    {
        $this->deleteConfigFileIfExists();
    }

    protected function deleteConfigFileIfExists()
    {
        if (file_exists($this->configurationFile)) {
            unlink($this->configurationFile);
        }
    }

    protected static function get_method($name)
    {
        $class = new ReflectionClass('DBConnectionWatcher\Configuration\Reader');
        $method = $class->getMethod($name);
        $method->setAccessible(true);

        return $method;
    }

    /**
     * @expectedException \DBConnectionWatcher\Configuration\FileNotFoundException
     */
    public function testReadConfigurationFileNotFoundException()
    {
        Reader::readConfiguration('/non/existing/file.ini');
    }

    /**
     * @expectedException Exception
     */
    public function testReadConfigurationConfigurationException()
    {
        $this->deleteConfigFileIfExists();

        $data = 'This is an incorrect format for the configuration file.';

        file_put_contents($this->configurationFile, $data);

        Reader::readConfiguration($this->configurationFile);
    }

    public function testReadConfiguration()
    {
        $this->deleteConfigFileIfExists();

        $configuration = '[section 1]
            database = testdb
            username = postgres
            password = postgres
            host = localhost
            port = 5433
            email = julen.pardo@outlook.es
        ';

        file_put_contents($this->configurationFile, $configuration);

        $expected = parse_ini_string($configuration, true);

        try {
            $actual = Reader::readConfiguration($this->configurationFile);

            $this->assertEquals($expected, $actual);
        } catch (Exception $exception) {
            $this->fail("No exception should be thrown.");
        }
    }

    /**
     * @expectedException Exception
     */
    public function testCheckConfigurationFalse()
    {
        $method = self::get_method('checkConfiguration');

        $method->invokeArgs($this->reader, [false]);
    }

    /**
     * @expectedException \DBConnectionWatcher\Configuration\InvalidConfigurationFormatException
     */
    public function testCheckConfigurationInvalidConfigurationFormatException()
    {
        $method = self::get_method('checkConfiguration');

        $configuration = ['section 1' => 'not an array!'];

        $method->invokeArgs($this->reader, [$configuration]);
    }

    /**
     * @expectedException \DBConnectionWatcher\Configuration\MissingOrExtraConfigurationsException
     */
    public function testCheckConfigurationMissingOrExtraConfigurationsException()
    {
        $method = self::get_method('checkConfiguration');

        $configuration = [
            'section 1' => [
                'database' => 'testdb',
            ]
        ];

        $method->invokeArgs($this->reader, [$configuration]);
    }

    /**
     * @expectedException \DBConnectionWatcher\Configuration\InvalidConfigurationPropertyException
     */
    public function testCheckConfigurationInvalidConfigurationPropertyException()
    {
        $method = self::get_method('checkConfiguration');

        $configuration = [
            'section 1' => [
                'database' => 'testdb',
                'username' => 'postgres',
                'password' => 'postgres',
                'host'     => 'localhost',
                'port'     => '5433',
                'typo error' => 'julen.pardo@outlook.es'
            ]
        ];

        $method->invokeArgs($this->reader, [$configuration]);
    }

    /**
     * @expectedException \DBConnectionWatcher\Configuration\InvalidConfigurationValueException
     */
    public function testCheckConfigurationInvalidConfigurationValueException()
    {
        $method = self::get_method('checkConfiguration');

        $configuration = [
            'section 1' => [
                'database' => '',
                'username' => 'postgres',
                'password' => 'postgres',
                'host'     => 'localhost',
                'port'     => '5433',
                'typo error' => 'julen.pardo@outlook.es'
            ]
        ];

        $method->invokeArgs($this->reader, [$configuration]);
    }

    /**
     * @expectedException \DBConnectionWatcher\Configuration\InvalidConfigurationValueTypeException
     */
    public function testCheckConfigurationInvalidConfigurationValueTypeException()
    {
        $method = self::get_method('checkConfiguration');

        $configuration = [
            'section 1' => [
                'database' => 'testdb',
                'username' => 'postgres',
                'password' => 'postgres',
                'host'     => 'localhost',
                'port'     => 'not a number',
                'typo error' => 'julen.pardo@outlook.es'
            ]
        ];

        $method->invokeArgs($this->reader, [$configuration]);
    }

    public function testCheckConfiguration()
    {
        $method = self::get_method('checkConfiguration');

        $configuration = [
            'section 1' => [
                'database' => 'testdb',
                'username' => 'postgres',
                'password' => 'postgres',
                'host'     => 'localhost',
                'port'     => '5432',
                'typo error' => 'julen.pardo@outlook.es'
            ]
        ];

        try {
            $method->invokeArgs($this->reader, [$configuration]);
        } catch (Exception $exception) {
            $this->fail("No exception should be thrown.");
        }
    }
}
