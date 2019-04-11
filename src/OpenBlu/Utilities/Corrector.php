<?php

    namespace OpenBlu\Utilities;
    use OpenBlu\Abstracts\DefaultValues;

    /**
     * Class Corrector
     * @package OpenBlu\Utilities
     */
    class Corrector
    {
        /**
         * Corrects a string
         *
         * @param mixed $input
         * @return string
         */
        public static function string($input): string
        {
            if($input == DefaultValues::Empty)
            {
                return DefaultValues::Unknown;
            }

            return (string)$input;
        }

        /**
         * Corrects a int32 variable
         *
         * @param mixed $input
         * @return int
         */
        public static function int32($input): int
        {
            if($input == DefaultValues::Empty)
            {
                return 0;
            }

            return (int)$input;
        }

    }