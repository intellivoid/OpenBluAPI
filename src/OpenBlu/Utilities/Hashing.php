<?php

    namespace OpenBlu\Utilities;

    /**
     * Class Hashing
     * @package OpenBlu\Utilities
     */
    class Hashing
    {
        /**
         * Calculates the Public ID for a update record
         *
         * @param string $data
         * @return string
         */
        public static function calculateUpdateRecordPublicID(string $data): string
        {
            return hash('whirlpool', $data);
        }

        /**
         * Calculates the Public ID for a VPN
         *
         * @param string $ipAddress
         * @return string
         */
        public static function calculateVPNPublicID(string $ipAddress): string
        {
            return hash('crc32b', hash('crc32', $ipAddress)) . hash('crc32', $ipAddress);
        }
    }