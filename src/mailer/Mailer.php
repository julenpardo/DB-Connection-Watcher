<?php

namespace DBConnectionWatcher\Mailer;

class Mailer
{
    const MAIL_HEADERS = "MIME-Version: 1.0\r\nContent-Type: text/html\r\n\r\n";

    const THRESHOLD_EXCEEDED_SUBJECT = "Warning: connection threshold exceeded in '%1' database";
    const THRESHOLD_EXCEEDED_MESSAGE = <<< HTML
        <html><body><p>The following database has generated an alert:</p>
        <ul>
            <li>Database: <b>%1</b></li>
            <li>In host: <b>%2</b></li>
            <li>Number of current connections: <b>%3</b></li>
            <li>Configured threshold: <b>%4</b></li>
        </ul></body></html>
HTML;


    const THRESHOLD_RETURN_BEHIND_SUBJECT = "Connection number in '%1' database is again behind the threshold";
    const THRESHOLD_RETURN_BEHIND_MESSAGE = <<< HTML
        <html><body><p>The following database has returned to normal situation, after having exceeded the configured
        threshold:</p>
        <ul>
            <li>Database: <b>%1</b></li>
            <li>In host: <b>%2</b></li>
            <li>Configured threshold: <b>%3</b></li>
        </ul></body></html>
HTML;

    /**
     * Sends a mail indicating that a database has exceeded the configured connection threshold.
     *
     * @param string $to Message addressee.
     * @param string $database The database name.
     * @param string $host The database host.
     * @param int $connectionNumber The current connection number.
     * @param int $threshold Connection threshold that generates the alerts.
     * @return bool If the mail has been correctly sent or not.
     */
    public static function sendThresholdExceededMail($to, $database, $host, $connectionNumber, $threshold)
    {
        $subject = str_replace('%1', $database, self::THRESHOLD_EXCEEDED_SUBJECT);

        $message = str_replace('%1', $database, self::THRESHOLD_EXCEEDED_MESSAGE);
        $message = str_replace('%2', $host, $message);
        $message = str_replace('%3', $connectionNumber, $message);
        $message = str_replace('%4', $threshold, $message);

        $sent = mail($to, $subject, $message, self::MAIL_HEADERS);

        return $sent;
    }

    /**
     * Sends a mail indicating that a database that has exceeded the threshold, has returned to normal situation.
     *
     * @param string $to Message addressee.
     * @param string $database The database name.
     * @param string $host The database host.
     * @param int $threshold Connection threshold that generates the alerts.
     * @return bool If the mail has been correctly sent or not.
     */
    public static function sendBehindThresholdMail($to, $database, $host, $threshold)
    {
        $subject = str_replace('%1', $database, self::THRESHOLD_RETURN_BEHIND_SUBJECT);

        $message = str_replace('%1', $database, self::THRESHOLD_RETURN_BEHIND_MESSAGE);
        $message = str_replace('%2', $host, $message);
        $message = str_replace('%3', $threshold, $message);

        $sent = mail($to, $subject, $message, self::MAIL_HEADERS);

        return $sent;
    }
}
