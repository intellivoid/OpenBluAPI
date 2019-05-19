<?php

    namespace OpenBlu\Exceptions;

    use Exception;
    use OpenBlu\Abstracts\ExceptionCodes;

    /**
     * Class PageNotFoundException
     * @package OpenBlu\Exceptions
     */
    class PageNotFoundException extends Exception
    {
        /**
         * PageNotFoundException constructor.
         */
        public function __construct()
        {
            parent::__construct('The page that was requested is not found', ExceptionCodes::PageNotFoundException, null);
        }
    }