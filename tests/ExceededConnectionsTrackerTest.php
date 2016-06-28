<?php

require_once(dirname(__FILE__) . '/../src/tracker/ExceededConnectionsTracker.php');
require_once(dirname(__FILE__) . '/../src/tracker/WriteException.php');

use DBConnectionWatcher\Tracker\WriteException;
use DBConnectionWatcher\Tracker\ExceededConnectionTracker;

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

    public function testReadAllDatabasesInvalidPath()
    {
        $path = 'this is an invalid path';
        $expected = [];
        $actual = ExceededConnectionTracker::readAllDatabases($path);

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

        $actual = ExceededConnectionTracker::readAllDatabases($this->file);

        $this->assertEquals($data, $actual);
    }
}
