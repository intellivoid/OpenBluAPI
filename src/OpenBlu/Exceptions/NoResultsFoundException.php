<?php


    namespace OpenBlu\Exceptions;

    use Exception;
    use OpenBlu\Abstracts\ExceptionCodes;

    /**
     * Class NoResultsFoundException
     * @package OpenBlu\Exceptions
     */
    class NoResultsFoundException extends Exception
    {
        /**
         * NoResultsFoundException constructor.
         */
        public function __construct()
        {
            parent::__construct('No results were found', ExceptionCodes::NoResultsFoundException, null);
        }
    }