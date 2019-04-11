<?php

    namespace OpenBlu\Exceptions;
    use Exception;
    use OpenBlu\Abstracts\ExceptionCodes;

    /**
     * Class InvalidSearchMethodException
     * @package OpenBlu\Exceptions
     */
    class InvalidSearchMethodException extends Exception
    {
        /**
         * InvalidSearchMethodException constructor.
         */
        public function __construct()
        {
            parent::__construct('The given search method is unsupported for this method', ExceptionCodes::InvalidSearchMethodException, null);
        }
    }