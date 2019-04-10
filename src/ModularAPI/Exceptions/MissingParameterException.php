<?php

    namespace ModularAPI\Exceptions;

    use ModularAPI\Abstracts\ExceptionCodes;

    class MissingParameterException extends \Exception
    {
        public $ParamerterName;

        /**
         * MissingParameterException constructor.
         * @param string $parameter_name
         */
        public function __construct(string $parameter_name)
        {
            $this->ParamerterName = $parameter_name;
            parent::__construct('A Required Parameter is missing', ExceptionCodes::MissingParameterException, null);
        }
    }