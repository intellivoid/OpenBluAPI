<?php

    namespace OpenBlu\Utilities;

    /**
     * Class Validator
     * @package OpenBlu\Utilities
     */
    class Validate
    {
        /**
         * Determines if the given input is a valid IPv4 or IPv6 Address
         *
         * @param string $input
         * @return bool
         */
        public static function IP(string $input): bool
        {
            if(filter_var($input, FILTER_VALIDATE_IP,FILTER_FLAG_IPV4) == true)
            {
                return true;
            }

            if(filter_var($input, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) == true)
            {
                return true;
            }

            return false;
        }
    }