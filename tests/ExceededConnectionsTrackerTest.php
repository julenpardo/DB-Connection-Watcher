<?php

require_once(dirname(__FILE__) . '/../src/tracker/ExceededConnectionsTracker.php');
require_once(dirname(__FILE__) . '/../src/tracker/WriteException.php');

use DBConnectionWatcher\Tracker\ExceededConnectionsTracker;

class ExceedConnectionsTrackerTest extends PHPUnit_Framework_Testcase
{
    protected $file;

    protected function setUp()
    {
        $this->file = dirname(__FILE__) . '/file.dat';
        $this->deleteFileIfExists();
    }

    protected function tearDown()
    {
        $this->deleteFileIfExists();
    }

    protected function deleteFileIfExists()
    {
        if (file_exists($this->file)) {
            unlink($this->file);
        }
    }

    protected function createFile()
    {
        file_put_contents($this->file, '');
    }

    protected static function getMethod($name)
    {
        $class = new ReflectionClass('DBConnectionWatcher\Tracker\ExceededConnectionsTracker');
        $method = $class->getMethod($name);
        $method->setAccessible(true);

        return $method;
    }

    public function testReadAllDatabasesInvalidPath()
    {
        $path = 'this is an invalid path';
        $expected = [];
        $actual = ExceededConnectionsTracker::readAllDatabases($path);

        $this->assertEquals($expected, $actual);
    }

    public function testReadAllDatabases()
    {
        $this->deleteFileIfExists();
        $this->createFile();

        $data = [
            '127.0.0.1' => [
                'localdb1',
                'localdb2'
            ],
            '127.0.0.2' => 'remotedb'
        ];

        foreach ($data as $host => $db) {
            if (is_array($db)) {
                foreach ($db as $singledb) {
                    file_put_contents($this->file, $host . ':' . $singledb . PHP_EOL, FILE_APPEND);
                }
            } else {
                file_put_contents($this->file, $host . ':' . $db . PHP_EOL, FILE_APPEND);
            }
        }

        $actual = ExceededConnectionsTracker::readAllDatabases($this->file);
        $this->assertEquals($data, $actual);
    }

    /**
     * @expectedException \DBConnectionWatcher\Tracker\WriteException
     */
    public function testSaveExceededDatabaseInvalidPath()
    {
        $path = '/invalid/path';
        $host = 'host';
        $database = 'database';

        ExceededConnectionsTracker::saveExceededDatabase($path, $host, $database);
    }

    public function testSaveExceededDatabase()
    {
        $this->deleteFileIfExists();
        $this->createFile();

        $host = '127.0.0.1';
        $db = 'testdb';

        $expected = $host . ExceededConnectionsTracker::SEPARATOR . $db;

        try {
            ExceededConnectionsTracker::saveExceededDatabase($this->file, $host, $db);

            $actual = file($this->file, FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);

            $this->assertEquals($expected, $actual[0]);
        } catch (Exception $exception) {
            $this->fail('No exception should be thrown: ' . $exception->getMessage());
        }
    }

    public function testCleanDatabases()
    {
        $this->deleteFileIfExists();
        $this->createFile();

        $line = '127.0.0.1:testdb';

        file_put_contents($this->file, $line);

        $method = $this->getMethod('cleanDatabases');
        $method->invokeArgs(null, [$this->file]);

        $expected = '';
        $actual = file_get_contents($this->file);

        $this->assertEquals($expected, $actual);
    }
}
