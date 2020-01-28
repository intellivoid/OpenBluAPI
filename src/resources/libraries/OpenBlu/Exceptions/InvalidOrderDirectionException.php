<?php


    namespace OpenBlu\Exceptions;

    use Exception;
    use OpenBlu\Abstracts\ExceptionCodes;

    /**
     * Class InvalidOrderDirectionException
     * @package OpenBlu\Exceptions
     */
    class InvalidOrderDirectionException extends Exception
    {
        /**
         * InvalidOrderDirectionException constructor.
         */
        public function __construct()
        {
            parent::__construct('The Order Direction Type is invalid', ExceptionCodes::InvalidOrderDirectionException, null);
        }
    }