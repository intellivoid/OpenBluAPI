<?php


    namespace OpenBlu\Exceptions;

    use Exception;
    use OpenBlu\Abstracts\ExceptionCodes;

    /**
     * Class ClientNotFoundException
     * @package OpenBlu\Exceptions
     */
    class ClientNotFoundException extends Exception
    {
        /**
         * ClientNotFoundException constructor.
         */
        public function __construct()
        {
            parent::__construct('The client was not found in the database', ExceptionCodes::ClientNotFoundException, null);
        }
    }