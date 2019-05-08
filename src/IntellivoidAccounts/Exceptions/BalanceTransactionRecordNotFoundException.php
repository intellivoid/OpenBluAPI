<?php


    namespace IntellivoidAccounts\Exceptions;

    use Exception;
    use IntellivoidAccounts\Abstracts\ExceptionCodes;

    /**
     * Class BalanceTransactionRecordNotFoundException
     * @package IntellivoidAccounts\Exceptions
     */
    class BalanceTransactionRecordNotFoundException extends Exception
    {
        /**
         * BalanceTransactionRecordNotFoundException constructor.
         */
        public function __construct()
        {
            parent::__construct('The Balance Transaction record was not found in the database', ExceptionCodes::BalanceTransactionRecordNotFoundException, false);
        }
    }