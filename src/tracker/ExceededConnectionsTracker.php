<?php

namespace DBConnectionWatcher\Tracker;

class ExceededConnectionTracker
{
    const SEPARATOR = ':';

    /**
     * Reads all the registered databases as exceeding.
     *
     * @param string $path The path where the data file is.
     * @return array [<host> => <db>], where <db> would be an array if the host has several databases.
     */
    public static function readAllDatabases($path)
    {
        $databases = [];
        $file = @file($path, FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);

        if ($file !== false) {
            foreach ($file as $line) {
                list($host, $database) = explode(self::SEPARATOR, $line);

                $databases = array_merge_recursive($databases, [$host => $database]);
            }

            self::cleanDatabases($path);
        }

        return $databases;
    }

    /**
     * Registers a database as exceeding, saving also the host, since only with the database name is not enough to
     * identify uniquely each database.
     * The format is the following: <host>:<database-name>, which is simple, and enough.
     *
     * @param string $path The path to the file where the data is saved.
     * @param string $host The host where the database is.
     * @param string $database The database that has exceeded the connection number.
     * @throws WriteException If an error occurs trying to write the data.
     */
    public static function saveExceededDatabase($path, $host, $database)
    {
        $line = $host . self::SEPARATOR . $database . PHP_EOL;
        $written = @file_put_contents($path, $line, FILE_APPEND | LOCK_EX);

        if (!$written) {
            throw new WriteException($path);
        }
    }

    /**
     * Removes all the databases registered as exceeding.
     *
     * @param string $path The path to the file where the data is saved.
     */
    protected static function cleanDatabases($path)
    {
        file_put_contents($path, '');
    }
}
