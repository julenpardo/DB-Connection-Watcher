<?php

namespace DBConnectionWatcher\Configuration;

define('DEFAULT_CONFIG_FILENAME', 'dbconnectionwatcher.ini');
define('DEFAULT_CONFIG_PATH', dirname(__FILE__) . '/../../' . DEFAULT_CONFIG_FILENAME);

class Reader
{
   private static $fieldsAndTypes = array(
        'database' => 'string',
        'username' => 'string',
        'password' => 'string',
        'host'     => 'string',
        'port'     => 'integer',
        'email'    => 'string'
    );

    /**
     * Reads the configuration from the .ini configuration file, with an array of [sections], which is another array
     * with the required fields for the configuration.
     *
     * @param string $configFilePath The path to the .ini configuration file.
     * @return array The read configuration array.
     * @throws \DBConnectionWatcher\Configuration\FileNotFoundException If the file has not been found.
     * @throws \Exception If the configuration file has not been properly written.
     */
    public static function readConfiguration($configFilePath = DEFAULT_CONFIG_PATH)
    {
        if (!file_exists($configFilePath)) {
            throw new FileNotFoundException("File '$configFilePath' not found.");
        }

        $configuration = parse_ini_file($configFilePath, true);

        try {
            self::checkConfiguration($configuration);
        } catch (\Exception $exception) {
            throw $exception;
        }

        return $configuration;
    }

    /**
     * Checks the configuration of the file, to ensure that:
     *  - The file has the correct format ([section], supposed each database, and then the configuration for each one).
     *  - The file has the required values (defined in class property $fieldsAndTypes).
     *  - The values are of correct type (only values that are supposed to be integers are checked, since the .ini
     *    parser returns every value as string).
     *
     * @param array $configuration The configuration array parsed with parse_ini_file.
     * @throws \Exception If the file has not the correct format.
     */
    private static function checkConfiguration($configuration)
    {
        if (!$configuration) {
            throw new \Exception('An error occurred parsing the configuration file.');
        }

        foreach ($configuration as $section => $data) {
            $invalidFileFormat = !is_array($data);

            if ($invalidFileFormat) {
                throw new \Exception('The file has an invalid format (may you forgot to put a [section]?).');
            }

            $keys = array_keys($data);

            $missingConfig = count($keys) !== count(self::$fieldsAndTypes);

            if ($missingConfig) {
                throw new \Exception("Missing (or extra) configuration(s) in '$section' section.");
            }

            foreach ($data as $key => $value) {
                $fieldNames = array_keys(self::$fieldsAndTypes);
                $invalidConfig = !in_array($key, $fieldNames);

                if ($invalidConfig) {
                    throw new \Exception("Invalid '$key' configuration in $section' section.");
                }

                if ($value === '') {
                    throw new \Exception("The '$key' configuration is empty.");
                }

                $expectedType = self::$fieldsAndTypes[$key];

                if ($expectedType === 'integer') {
                    $invalidNumber = !is_numeric($value);

                    if ($invalidNumber) {
                        throw new \Exception("Invalid type of '$key' configuration: expecting $expectedType type and "
                            . "got '$value' value, in section '$section'.");
                    }
                }
            }
        }
    }
}
