<?php


    namespace OpenBlu\Exceptions;

    use Exception;
    use OpenBlu\Abstracts\ExceptionCodes;

    /**
     * Class InvalidFilterTypeException
     * @package OpenBlu\Exceptions
     */
    class InvalidFilterTypeException extends Exception
    {
        /**
         * InvalidFilterTypeException constructor.
         */
        public function __construct()
        {
            parent::__construct('The given filter type is invalid', ExceptionCodes::InvalidFilterTypeException, null);
        }
    }