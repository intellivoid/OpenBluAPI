<?php

    namespace OpenBlu\Exceptions;

    use Exception;
    use OpenBlu\Abstracts\ExceptionCodes;

    /**
     * Class ConfigurationNotFoundException
     * @package OpenBlu\Exceptions
     */
    class ConfigurationNotFoundException extends Exception
    {
        /**
         * ConfigurationNotFoundException constructor.
         */
        public function __construct()
        {
            parent::__construct('The configuration file for the OpenBlu library was not found', ExceptionCodes::ConfigurationNotFoundException, null);
        }
    }