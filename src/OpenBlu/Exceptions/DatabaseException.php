<?php

    namespace OpenBlu\Exceptions;

    use Exception;
    use OpenBlu\Abstracts\ExceptionCodes;

    /**
     * Class DatabaseException
     * @package OpenBlu\Exceptions
     */
    class DatabaseException extends Exception
    {
        /**
         * @var string
         */
        private $error_message;

        /**
         * @var string
         */
        private $query;

        /**
         * DatabaseException constructor.
         * @param string $error_message
         * @param string $query
         */
        public function __construct(string $error_message, string $query)
        {
            $this->error_message = $error_message;
            $this->query = $query;
            parent::__construct('There was a database error', ExceptionCodes::DatabaseException, null);
        }

        /**
         * @return string
         */
        public function getErrorMessage(): string
        {
            return $this->error_message;
        }

        /**
         * @return string
         */
        public function getQuery(): string
        {
            return $this->query;
        }

    }