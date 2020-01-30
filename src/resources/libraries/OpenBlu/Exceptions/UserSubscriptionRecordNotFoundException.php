<?php


    namespace OpenBlu\Exceptions;


    use Exception;
    use OpenBlu\Abstracts\ExceptionCodes;

    /**
     * Class UserSubscriptionRecordNotFoundException
     * @package OpenBlu\Exceptions
     */
    class UserSubscriptionRecordNotFoundException extends Exception
    {
        /**
         * UserSubscriptionRecordNotFoundException constructor.
         */
        public function __construct()
        {
            parent::__construct("The user subscription was not found in the database", ExceptionCodes::UserSubscriptionRecordNotFoundException, null);
        }
    }