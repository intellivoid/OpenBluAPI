<?php

    namespace OpenBlu\Utilities;

    /**
     * Class OpenVPNConfiguration
     * @package OpenBlu\Utilities
     */
    class OpenVPNConfiguration
    {
        /**
         * Parses the configuration and retrieves all important values regarding the configuration
         *
         * @param string $data
         * @return array
         */
        public static function parseConfiguration(string $data): array
        {
            $data = self::stripConfiguration($data);
            $results = array();

            $results['ca'] = self::getTag($data, 'ca');
            $results['cert'] = self::getTag($data, 'cert');
            $results['key'] = self::getTag($data, 'key');

            $data = str_ireplace(sprintf("<ca>\r\n%s\r\n</ca>", $results['ca']), '', $data);
            $data = str_ireplace(sprintf("<cert>\r\n%s\r\n</cert>", $results['cert']), '', $data);
            $data = str_ireplace(sprintf("<key>\r\n%s\r\n</key>", $results['key']), '', $data);

            $results['parameters'] = self::extractParameters($data);

            return $results;
        }

        /**
         * Strips the configuration from empty line breaks and comments
         *
         * @param string $data
         * @return string
         */
        public static function stripConfiguration(string $data): string
        {
            $output = '';

            foreach(explode("\r\n", $data) as $line)
            {
                if(strlen($line) > 0)
                {
                    if(substr($line, 0, 1) !== '#')
                    {
                        if(substr($line, 0, 1) !== ';')
                        {
                            $output .= $line . "\r\n";
                        }
                    }
                }
            }

            return $output;
        }

        /**
         * Extracts all parameters from the configuration file
         *
         * @param string $data
         * @return array
         */
        public static function extractParameters(string $data): array
        {
            $parameters = [];

            foreach($parametersExploded = explode("\r\n", $data) as $line)
            {
                $paramerter = explode(' ', $line);
                if(isset($paramerter[1]))
                {
                    if(isset($paramerter[2]))
                    {
                        $parameters[$paramerter[0]] = $paramerter[1] . ' ' . $paramerter[2];
                    }
                    else
                    {
                        $parameters[$paramerter[0]] = $paramerter[1];
                    }
                }
                else
                {
                    $parameters[$paramerter[0]] = null;
                }
            }

            return $parameters;
        }

        /**
         * Extracts a tag from the configuration
         *
         * @param string $data
         * @param string $tag
         * @return string
         */
        public static function getTag(string $data, string $tag)
        {
            preg_match("#<\s*?$tag\b[^>]*>(.*?)</$tag\b[^>]*>#s", $data, $matches);
            return trim($matches[1]);
        }
    }