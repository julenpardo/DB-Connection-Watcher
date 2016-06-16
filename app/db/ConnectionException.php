<?php

namespace DBConnectionWatcher\DB;

class ConnectionException extends \Exception
{
    const MESSAGE = 'An error occur when trying to %1 PostgreSQL database connection: ';

    /**
     * ConnectionException constructor.
     *
     * @param string $action If opening or closing.
     * @param int $pgError PostgreSQL error string.
     */
    public function __construct($action, $pgError)
    {
        $message = str_replace('%1', $action, self::MESSAGE);

        parent::__construct($message . $pgError);
    }
}
