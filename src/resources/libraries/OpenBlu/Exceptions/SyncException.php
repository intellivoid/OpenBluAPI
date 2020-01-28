<?php

    namespace OpenBlu\Exceptions;

    use Exception;
    use OpenBlu\Abstracts\ExceptionCodes;

    /**
     * Class SyncException
     * @package OpenBlu\Exceptions
     */
    class SyncException extends Exception
    {
        /**
         * @var string
         */
        public $curlException;

        /**
         * SyncException constructor.
         * @param string $curlException
         */
        public function __construct(string $curlException)
        {
            $this->curlException = $curlException;
            parent::__construct('There was an error when trying to sync with the remote server', ExceptionCodes::SyncException, null);
        }
    }