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
    protected $configurationFile = 'configurationTest.ini';

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
}
