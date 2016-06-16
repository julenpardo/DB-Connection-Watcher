<?php

namespace DBConnectionWatcher;

use DBConnectionWatcher\Configuration\ConfigurationException;
use DBConnectionWatcher\DB\ConnectionException;
use DBConnectionWatcher\DB\DBInterface;
use DBConnectionWatcher\Configuration\Reader;
use DBConnectionWatcher\DB\DBFactory;
use DBConnectionWatcher\DB\PreparedStatementCreationException;
use DBConnectionWatcher\Mailer\Mailer;

class DBConnectionManager {

    const ERROR_CONFIGURATION_EXCEPTION = 1;
    const ERROR_CONNECTION_EXCEPTION = 2;
    const ERROR_PREPARED_STATEMENT_EXCEPTION = 3;
    const SUCCESS = 0;

    /**
     * The "main" function: reads the configuration, and checks the state of each database read from each configured
     * database.
     *
     * @return int Status code; 0 if no error happened, others if an error occurred. Yes, it can be overwritten. But
     * only one value can be returned.
     */
    public static function run()
    {
        try {
            $configuration = Reader::readConfiguration();
        } catch (ConfigurationException $configurationException) {
            return self::ERROR_CONFIGURATION_EXCEPTION;
        }

        $status = self::SUCCESS;

        foreach ($configuration as $dbConfiguration) {
            $db = DBFactory::getInstance(array_values($dbConfiguration));
            $email = $dbConfiguration['email'];
            $connectionThreshold = $dbConfiguration['connection_threshold'];

            try {
                self::checkStatus($db, $email, $connectionThreshold);
            } catch (ConnectionException $connectionException) {
                error_log($connectionException->getMessage());
                $status = self::ERROR_CONNECTION_EXCEPTION;
            } catch (PreparedStatementCreationException $preparedStatementException) {
                error_log($preparedStatementException->getMessage());
                $status = self::ERROR_PREPARED_STATEMENT_EXCEPTION;
            }
        }

        return $status;
    }

    /**
     * Queries the current connection number and compares it with the established threshold, sending the alert to the
     * specified emails if its exceeded.
     *
     * @param DBInterface $db The database to check
     * @param string $email The emails to send the notifications to.
     * @param int $connectionThreshold The connection threshold that, once exceeded, generates the alert.
     * @throws ConnectionException If an error occurs connecting/disconnecting to database.
     * @throws PreparedStatementCreationException If an error occur
     */
    protected static function checkStatus($db, $email, $connectionThreshold)
    {
        try {
            $db->connect();
            $currentConnections = $db->queryConnectionNumber();

            if ($currentConnections > $connectionThreshold) {
                Mailer::sendThresholdExceededMail(
                    $email,
                    $db->getDatabase(),
                    $db->getHost(),
                    $currentConnections,
                    $connectionThreshold
                );
            }

            $db->disconnect();
        } catch (ConnectionException $connectionException) {
            throw $connectionException;
        } catch (PreparedStatementCreationException $preparedStatementException) {
            throw $preparedStatementException;
        }
    }
}
