<?php


    namespace OpenBlu\Exceptions;

    use Exception;
    use OpenBlu\Abstracts\ExceptionCodes;

    /**
     * Class LimitExceedingException
     * @package OpenBlu\Exceptions
     */
    class LimitExceedingException extends Exception
    {
        /**
         * LimitExceedingException constructor.
         */
        public function __construct()
        {
            parent::__construct('The request limit cannot be greater than 100', ExceptionCodes::LimitExceedingException, null);
        }
    }