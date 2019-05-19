<?php


    namespace OpenBlu\Exceptions;

    use Exception;
    use OpenBlu\Abstracts\ExceptionCodes;

    /**
     * Class PlanNotFoundException
     * @package OpenBlu\Exceptions
     */
    class PlanNotFoundException extends Exception
    {
        /**
         * PlanNotFoundException constructor.
         */
        public function __construct()
        {
            parent::__construct('The plan was not found in the database', ExceptionCodes::PlanNotFoundException, null);
        }
    }