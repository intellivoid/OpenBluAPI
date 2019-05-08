<?php


    namespace OpenBlu\Exceptions;

    use OpenBlu\Abstracts\ExceptionCodes;

    /**
     * Class InvalidClientPropertyException
     * @package OpenBlu\Exceptions
     */
    class InvalidClientPropertyException extends \Exception
    {
        /**
         * InvalidClientPropertyException constructor.
         */
        public function __construct()
        {
            parent::__construct('The client property is invalid', ExceptionCodes::InvalidClientPropertyException, null);
        }
    }