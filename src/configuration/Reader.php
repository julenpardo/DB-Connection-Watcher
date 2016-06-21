<?php

namespace DBConnectionWatcher\Configuration;

class Reader
{
    private static $fieldsAndTypes = array(
        'database'             => 'string',
        'username'             => 'string',
        'password'             => 'string',
        'host'                 => 'string',
        'port'                 => 'integer',
        'email'                => 'string',
        'connection_threshold' => 'integer',
        'dbms'                 => array(
            'postgresql'
        )
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
    public static function readConfiguration($configFilePath)
    {
        if (!file_exists($configFilePath)) {
            throw new FileNotFoundException("File '$configFilePath' not found.");
        }

        $configuration = parse_ini_file($configFilePath, true);

        try {
            self::checkConfiguration($configuration);
        } catch (ConfigurationException $exception) {
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
     * @throws InvalidConfigurationFormatException If the .ini file has an incorrect format.
     * @throws InvalidConfigurationPropertyException If the .ini file has a property that is not considered.
     * @throws InvalidConfigurationValueException If a property has an incorrect value.
     * @throws InvalidConfigurationValueTypeException If a property has a value of incorrect format.
     * @throws MissingOrExtraConfigurationsException If the .ini file has not the number of expected properties.
     * @throws NonAcceptedValueException If the .ini has a value in a property that is not between the accepted ones.
     * @throws \Exception If the .ini file has not the correct format.
     */
    private static function checkConfiguration($configuration)
    {
        if (!$configuration) {
            throw new ConfigurationException('An error occurred parsing the configuration file.');
        }

        foreach ($configuration as $section => $data) {
            $invalidConfigurationFormat = !is_array($data);

            if ($invalidConfigurationFormat) {
                throw new InvalidConfigurationFormatException();
            }

            $keys = array_keys($data);
            $missingConfig = count($keys) !== count(self::$fieldsAndTypes);

            if ($missingConfig) {
                throw new MissingOrExtraConfigurationsException($section);
            }

            foreach ($data as $key => $value) {
                $fieldNames = array_keys(self::$fieldsAndTypes);
                $invalidConfig = !in_array($key, $fieldNames);

                if ($invalidConfig) {
                    throw new InvalidConfigurationPropertyException($key, $section);
                }

                if ($value === '') {
                    throw new InvalidConfigurationValueException($key);
                }

                $expectedType = self::$fieldsAndTypes[$key];

                if ($expectedType === 'integer') {
                    $invalidNumber = !is_numeric($value);

                    if ($invalidNumber) {
                        throw new InvalidConfigurationValueTypeException($key, $expectedType, $value, $section);
                    }
                } elseif (is_array($expectedType)) {
                    $nonAcceptedValue = !in_array(strtolower($value), $expectedType);

                    if ($nonAcceptedValue) {
                        throw new NonAcceptedValueException($key, $section, $expectedType);
                    }
                }
            }
        }
    }
}
