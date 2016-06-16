<?php

namespace DBConnectionWatcher\DB;

use DBConnectionWatcher\Configuration\ConfigurationException;
use DBConnectionWatcher\DB\DBMS\PostgreSQL;

class DBFactory
{
    const DBMS_POSTGRESQL = 'postgresql';

    /**
     * Creates the required database instance, i.e., to deal which each database management system, depending on the
     * configuration read.
     *
     * @param array $dbConfiguration The configuration array for the given section.
     * @return DBInterface An instance that implements this interface.
     * @throws ConfigurationException If the received dbms is incorrect. This should never happen, because is checked
     * before calling this function.
     */
    public static function getInstance($dbConfiguration)
    {
        $dbms = $dbConfiguration['dbms'];

        switch ($dbms) {
            case self::DBMS_POSTGRESQL:
                $database = new PostgreSQL(
                    $dbConfiguration['database'],
                    $dbConfiguration['username'],
                    $dbConfiguration['password'],
                    $dbConfiguration['host'],
                    $dbConfiguration['port']
                );
                break;

            default:
                throw new ConfigurationException("Non valid '$dbms' dbms configuration.");
        }

        return $database;
    }
}
