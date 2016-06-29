<?php

/**
 * Exception class for prepared statement exceptions.
 *
 * @author Julen Pardo
 */

namespace DBConnectionWatcher\DB;

/**
 * Class PreparedStatementCreationException.
 *
 * @package DBConnectionWatcher\DB
 * @author  Julen Pardo
 */
class PreparedStatementCreationException extends \Exception
{
    /**
     * Exception message.
     * @const
     */
    const MESSAGE = 'An error occurred when creating the prepared statement for the query: ';

    /**
     * PreparedStatementCreationException constructor.
     */
    public function __construct()
    {
        parent::__construct(self::MESSAGE . error_get_last()['message']);
    }
}
