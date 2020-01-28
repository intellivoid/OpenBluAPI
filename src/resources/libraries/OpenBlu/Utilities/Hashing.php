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

        /**
         * Calculates the Public ID for a client
         *
         * @param string $client_uid
         * @param string $client_name
         * @param int $registered_timestamp
         * @return string
         */
        public static function calculateClientPublicID(string $client_uid, string $client_name, int $registered_timestamp): string
        {
            return hash('crc32b', $client_uid) . hash('crc32b', $client_name) . hash('crc32b', $registered_timestamp);
        }
    }