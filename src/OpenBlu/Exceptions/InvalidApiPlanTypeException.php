<?php


    namespace OpenBlu\Exceptions;

    use Exception;
    use OpenBlu\Abstracts\ExceptionCodes;

    /**
     * Class InvalidApiPlanTypeException
     * @package OpenBlu\Exceptions
     */
    class InvalidApiPlanTypeException extends Exception
    {
        /**
         * InvalidApiPlanTypeException constructor.
         */
        public function __construct()
        {
            parent::__construct('The selected API Plan type is invalid', ExceptionCodes::InvalidApiPlanTypeException, null);
        }
    }