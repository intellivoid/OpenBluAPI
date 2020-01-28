<?php


    namespace OpenBlu\Exceptions;

    use Exception;
    use OpenBlu\Abstracts\ExceptionCodes;

    /**
     * Class InvalidFilterValueException
     * @package OpenBlu\Exceptions
     */
    class InvalidFilterValueException extends Exception
    {
        /**
         * InvalidFilterValueException constructor.
         */
        public function __construct()
        {
            parent::__construct('The filter value for this filter type is invalid', ExceptionCodes::InvalidFilterValueException, null);
        }
    }