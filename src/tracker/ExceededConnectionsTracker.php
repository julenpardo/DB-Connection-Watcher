<?php

namespace DBConnectionWatcher\Tracker;

class ExceededConnectionTracker
{
    const SEPARATOR = ':';

    public static function readAllDatabases($path)
    {
        $databases = [];
        $file = file($path, FILE_SKIP_EMPTY_LINES);

        if ($file !== false) {
            foreach ($file as $line) {
                list($host, $database) = explode(self::SEPARATOR, $line);

                $databases = array_merge_recursive($databases, [$host => $database]);
            }

            self::cleanDatabases($path);
        }

        return $databases;
    }

    public static function saveExceededDatabase($path, $host, $database)
    {
        $line = $host . self::SEPARATOR . $database . PHP_EOL;
        $written = file_put_contents($path, $line, FILE_APPEND | LOCK_EX);

        if (!$written) {
            throw new WriteException($path);
        }
    }

    protected static function cleanDatabases($path)
    {
        file_put_contents($path, '');
    }
}
