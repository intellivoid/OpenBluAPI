<?php

    namespace OpenBlu\Exceptions;

    use Exception;
    use OpenBlu\Abstracts\ExceptionCodes;

    /**
     * Class UpdateRecordNotFoundException
     * @package OpenBlu\Exceptions
     */
    class UpdateRecordNotFoundException extends Exception
    {
        /**
         * UpdateRecordNotFoundException constructor.
         */
        public function __construct()
        {
            parent::__construct('The update record was not found', ExceptionCodes::UpdateRecordNotFoundException, null);
        }
    }