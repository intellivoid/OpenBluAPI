<?php

    namespace OpenBlu\Exceptions;

    use Exception;
    use OpenBlu\Abstracts\ExceptionCodes;

    /**
     * Class InvalidIPAddressException
     * @package OpenBlu\Exceptions
     */
    class InvalidIPAddressException extends Exception
    {
        /**
         * InvalidIPAddressException constructor.
         */
        public function __construct()
        {
            parent::__construct('The given IP Address is invalid', ExceptionCodes::InvalidIPAddressException, null);
        }
    }