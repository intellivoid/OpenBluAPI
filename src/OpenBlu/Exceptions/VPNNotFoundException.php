<?php

    namespace OpenBlu\Exceptions;

    use Exception;
    use OpenBlu\Abstracts\ExceptionCodes;

    /**
     * Class VPNNotFoundException
     * @package OpenBlu\Exceptions
     */
    class VPNNotFoundException extends Exception
    {
        /**
         * VPNNotFoundException constructor.
         */
        public function __construct()
        {
            parent::__construct('The VPN was not found', ExceptionCodes::VPNNotFoundException, null);
        }
    }