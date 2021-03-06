<?php

/**
 * Exception class for mail sending exceptions.
 *
 * @author Julen Pardo
 */

namespace DBConnectionWatcher\Mailer;

/**
 * Class MailSendException.
 *
 * @package DBConnectionWatcher\Mailer
 * @author  Julen Pardo
 */
class MailSendException extends \Exception
{
    /**
     * Exception message.
     * @const
     */
    const MESSAGE = 'An error occur when sending the mail: ';

    /**
     * MailSendException constructor.
     */
    public function __construct()
    {
        parent::__construct(self::MESSAGE . error_get_last()['message']);
    }
}
