<?php


    namespace OpenBlu\Exceptions;

    use Exception;
    use OpenBlu\Abstracts\ExceptionCodes;

    /**
     * Class InvalidOrderByTypeException
     * @package OpenBlu\Exceptions
     */
    class InvalidOrderByTypeException extends Exception
    {
        /**
         * InvalidOrderByTypeException constructor.
         */
        public function __construct()
        {
            parent::__construct('The Order Type is exception', ExceptionCodes::InvalidOrderByTypeException, null);
        }
    }