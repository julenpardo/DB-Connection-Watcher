<?php

namespace DBConnectionWatcher;

define('DEFAULT_CONFIG_FILENAME', 'dbconnectionwatcher.ini');
define('DEFAULT_CONFIG_PATH', dirname(__FILE__) . '/../' . DEFAULT_CONFIG_FILENAME);

use DBConnectionWatcher\Configuration\ConfigurationException;
use DBConnectionWatcher\DB\ConnectionException;
use DBConnectionWatcher\DB\DBInterface;
use DBConnectionWatcher\Configuration\Reader;
use DBConnectionWatcher\DB\DBFactory;
use DBConnectionWatcher\DB\PreparedStatementCreationException;
use DBConnectionWatcher\Mailer\Mailer;
use DBConnectionWatcher\Mailer\MailSendException;

class DBConnectionWatcher
{
    const ERROR_CONFIGURATION_EXCEPTION = 1;
    const ERROR_CONNECTION_EXCEPTION = 2;
    const ERROR_PREPARED_STATEMENT_EXCEPTION = 3;
    const ERROR_MAIL_SEND_EXCEPTION = 4;
    const SUCCESS = 0;

    protected $mailer;

    /**
     * DBConnectionWatcher constructor.
     */
    public function __construct()
    {
        $this->mailer = new Mailer();
    }

    /**
     * Mailer object setter. This is only for mocking in tests.
     *
     * @param Mailer $mailer The mailer instance.
     */
    public function setMailer($mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * The "main" function: reads the configuration, and checks the state of each database read from each configured
     * database.
     *
     * To end the function, exit() function is used (with terminate() class function wrapper)instead of returning a status
     * value, because "return" does not return the status to de environment, and this has to be delegated to PHP using
     * exit() function.
     */
    public function run()
    {
        try {
            $configuration = Reader::readConfiguration(DEFAULT_CONFIG_PATH);
        } catch (ConfigurationException $configurationException) {
            error_log($configurationException->getMessage());
            $this->terminate(self::ERROR_CONFIGURATION_EXCEPTION);
        }

        foreach ($configuration as $dbConfiguration) {
            $db = DBFactory::getInstance($dbConfiguration);
            $email = $dbConfiguration['email'];
            $connectionThreshold = $dbConfiguration['connection_threshold'];

            try {
                $this->checkStatus($db, $email, $connectionThreshold);
            } catch (ConnectionException $connectionException) {
                error_log($connectionException->getMessage());
                $this->terminate(self::ERROR_CONNECTION_EXCEPTION);
            } catch (PreparedStatementCreationException $preparedStatementException) {
                error_log($preparedStatementException->getMessage());
                $this->terminate(self::ERROR_PREPARED_STATEMENT_EXCEPTION);
            } catch (MailSendException $mailSendException) {
                error_log($mailSendException->getMessage());
                $this->terminate(self::ERROR_MAIL_SEND_EXCEPTION);
            }
        }

        $this->terminate(self::SUCCESS);
    }

    /**
     * Queries the current connection number and compares it with the established threshold, sending the alert to the
     * specified emails if its exceeded.
     *
     * @param DBInterface $db The database to check
     * @param string $email The emails to send the notifications to.
     * @param int $connectionThreshold The connection threshold that, once exceeded, generates the alert.
     * @throws ConnectionException If an error occurs connecting/disconnecting to database.
     * @throws PreparedStatementCreationException If an error occurs creating the prepared statement for the query.
     * @throws MailSendException If an error occurs sending the mail.
     */
    protected function checkStatus($db, $email, $connectionThreshold)
    {
        try {
            $db->connect();
            $currentConnections = $db->queryConnectionNumber();

            if ($currentConnections > $connectionThreshold) {
                $this->mailer->sendThresholdExceededMail(
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
        } catch (MailSendException $mailSendException) {
            throw $mailSendException;
        }
    }

    /**
     * A wrapper for exit() function, a "PHP killer" function. This is just for mocking the execution termination in unit
     * tests.
     *
     * @param int $code The exit code.
     */
    protected function terminate($code)
    {
        exit($code);
    }
}
