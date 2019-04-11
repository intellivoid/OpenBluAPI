<?php

    namespace OpenBlu\Managers;

    use OpenBlu\Exceptions\DatabaseException;
    use OpenBlu\Exceptions\InvalidIPAddressException;
    use OpenBlu\Exceptions\InvalidSearchMethodException;
    use OpenBlu\Exceptions\PageNotFoundException;
    use OpenBlu\Exceptions\VPNNotFoundException;
    use OpenBlu\Objects\VPN;
    use OpenBlu\OpenBlu;
    use OpenBlu\Utilities\Hashing;
    use OpenBlu\Utilities\Validate;

    /**
     * Class VPNManager
     * @package OpenBlu\Managers
     */
    class VPNManager
    {
        /**
         * @var OpenBlu
         */
        private $openBlu;

        /**
         * VPNManager constructor.
         * @param OpenBlu $openBlu
         */
        public function __construct(OpenBlu $openBlu)
        {
            $this->openBlu = $openBlu;
        }

        /**
         * @param VPN $vpn
         * @return bool
         * @throws DatabaseException
         * @throws InvalidIPAddressException
         */
        public function registerVPN(VPN $vpn): bool
        {
            if(Validate::IP($vpn->IP) == false)
            {
                throw new InvalidIPAddressException();
            }

            $PublicID = $this->openBlu->database->real_escape_string(Hashing::calculateVPNPublicID($vpn->IP));
            $HostName = $this->openBlu->database->real_escape_string($vpn->HostName);
            $IPAddress = $this->openBlu->database->real_escape_string($vpn->IP);
            $Score = (int)$vpn->Score;
            $Ping = (int)$vpn->Ping;
            $Country = $this->openBlu->database->real_escape_string($vpn->Country);
            $CountryShort = $this->openBlu->database->real_escape_string($vpn->CountryShort);
            $Sessions = (int)$vpn->Sessions;
            $TotalSessions = (int)$vpn->TotalSessions;

            $Created = (int)time();
            $LastUpdated = (int)time();

            $ConfigurationParameters = $this->openBlu->database->real_escape_string(json_encode($vpn->ConfigurationParameters));
            $CertificateAuthority = $this->openBlu->database->real_escape_string($vpn->CertificateAuthority);
            $Certificate = $this->openBlu->database->real_escape_string($vpn->Certificate);
            $Key = $this->openBlu->database->real_escape_string($vpn->Key);

            $Query = "INSERT INTO `vpns` (public_id, host_name, ip_address, score, ping, country, country_short, sessions, total_sessions, configuration_parameters, certificate_authority, certificate, `key`, last_updated, created) VALUES ('$PublicID', '$HostName', '$IPAddress', $Score, $Ping, '$Country', '$CountryShort', $Sessions, $TotalSessions, '$ConfigurationParameters', '$CertificateAuthority', '$Certificate', '$Key', $LastUpdated, $Created)";
            $QueryResults = $this->openBlu->database->query($Query);

            if($QueryResults == true)
            {
                return true;
            }
            else
            {
                throw new DatabaseException($this->openBlu->database->error, $Query);
            }
        }

        /**
         * Gets an existing VPN from the Database
         *
         * @param string $searchMethod
         * @param string|\OpenBlu\Abstracts\SearchMethods\VPN $input
         * @return VPN
         * @throws DatabaseException
         * @throws InvalidSearchMethodException
         * @throws VPNNotFoundException
         */
        public function getVPN(string $searchMethod, string $input): VPN
        {
            switch($searchMethod)
            {
                case \OpenBlu\Abstracts\SearchMethods\VPN::byPublicID:
                    $searchMethod = $this->openBlu->database->real_escape_string($searchMethod);
                    $input = "\"" . $this->openBlu->database->real_escape_string($input) . "\"";
                    break;

                case \OpenBlu\Abstracts\SearchMethods\VPN::byID:
                    $searchMethod = $this->openBlu->database->real_escape_string($searchMethod);
                    $input = (int)$input;
                    break;

                case \OpenBlu\Abstracts\SearchMethods\VPN::byIP:
                    $searchMethod = $this->openBlu->database->real_escape_string($searchMethod);
                    $input = "\"" . $this->openBlu->database->real_escape_string($input) . "\"";
                    break;

                default:
                    throw new InvalidSearchMethodException();
            }

            $Query = "SELECT id, public_id, host_name, ip_address, score, ping, country, country_short, sessions, total_sessions, configuration_parameters, certificate_authority, certificate, `key`, created, last_updated FROM `vpns` WHERE $searchMethod=$input";
            $QueryResults = $this->openBlu->database->query($Query);

            if($QueryResults == false)
            {
                throw new DatabaseException($this->openBlu->database->error, $Query);
            }
            else
            {
                if($QueryResults->num_rows !== 1)
                {
                    throw new VPNNotFoundException();
                }

                $Row = $QueryResults->fetch_array(MYSQLI_ASSOC);
                $Row['configuration_parameters'] = json_decode($Row['configuration_parameters'], true);
                return VPN::fromArray($Row);
            }
        }

        /**
         * Updates an existing VPN in the database
         *
         * @param VPN $vpn
         * @return bool
         * @throws DatabaseException
         * @throws InvalidIPAddressException
         * @throws VPNNotFoundException
         * @throws InvalidSearchMethodException
         */
        public function updateVPN(VPN $vpn): bool
        {
            if($this->vpnExists(\OpenBlu\Abstracts\SearchMethods\VPN::byID, $vpn->ID) == false)
            {
                throw new VPNNotFoundException();
            }

            if(Validate::IP($vpn->IP) == false)
            {
                throw new InvalidIPAddressException();
            }

            $ID = (int)$vpn->ID;
            $HostName = $this->openBlu->database->real_escape_string($vpn->HostName);
            $IPAddress = $this->openBlu->database->real_escape_string($vpn->IP);
            $Score = (int)$vpn->Score;
            $Ping = (int)$vpn->Ping;
            $Country = $this->openBlu->database->real_escape_string($vpn->Country);
            $CountryShort = $this->openBlu->database->real_escape_string(strtoupper($vpn->CountryShort));
            $Sessions = (int)$vpn->Sessions;
            $TotalSessions = (int)$vpn->TotalSessions;
            $LastUpdated = (int)time();

            $ConfigurationParameters = $this->openBlu->database->real_escape_string(json_encode($vpn->ConfigurationParameters));
            $CertificateAuthority = $this->openBlu->database->real_escape_string($vpn->CertificateAuthority);
            $Certificate = $this->openBlu->database->real_escape_string($vpn->Certificate);
            $Key = $this->openBlu->database->real_escape_string($vpn->Key);

            $Query = "UPDATE `vpns` SET host_name='$HostName', ip_address='$IPAddress', score=$Score, ping=$Ping, country='$Country', country_short='$CountryShort', sessions=$Sessions, total_sessions=$TotalSessions, configuration_parameters='$ConfigurationParameters', certificate_authority='$CertificateAuthority', certificate='$Certificate', `key`='$Key', last_updated=$LastUpdated WHERE id=$ID";
            $QueryResults = $this->openBlu->database->query($Query);

            if($QueryResults == true)
            {
                return true;
            }
            else
            {
                throw new DatabaseException($this->openBlu->database->error, $Query);
            }
        }

        /**
         * Determines if the VPN exists in the database
         *
         * @param string|\OpenBlu\Abstracts\SearchMethods\VPN $searchMethod
         * @param string $input
         * @return bool
         * @throws DatabaseException
         * @throws InvalidSearchMethodException
         */
        public function vpnExists(string $searchMethod, string $input): bool
        {
            try
            {
                $this->getVPN($searchMethod, $input);
                return true;
            }
            catch(VPNNotFoundException $VPNNotFoundException)
            {
                return false;
            }
        }

        /**
         * Syncs a VPN if it exists or not
         *
         * @param VPN $vpn
         * @return bool
         * @throws DatabaseException
         * @throws InvalidIPAddressException
         * @throws InvalidSearchMethodException
         * @throws VPNNotFoundException
         */
        public function syncVPN(VPN $vpn): bool
        {
           if($this->vpnExists(\OpenBlu\Abstracts\SearchMethods\VPN::byPublicID, $vpn->PublicID) == false)
           {
                $this->registerVPN($vpn);
                return true;
           }

           $realVPN = $this->getVPN(\OpenBlu\Abstracts\SearchMethods\VPN::byPublicID, $vpn->PublicID);

           $vpn->ID = $realVPN->ID;
           $vpn->PublicID = $realVPN->PublicID;
           $vpn->Created = $realVPN->Created;
           $vpn->LastUpdated = $realVPN->LastUpdated;

           $this->updateVPN($vpn);

           return true;
        }

        /**
         * Returns the total amount of servers available in the database
         *
         * @return int
         */
        public function totalServers(): int
        {
            $Query = "SELECT COUNT(id) AS total FROM `vpns`";
            $QueryResults = $this->openBlu->database->query($Query);

            if($QueryResults == false)
            {
                return 0;
            }

            return (int)$QueryResults->fetch_array()['total'];
        }

        /**
         * Returns the current amount of sessions in total
         *
         * @return int
         */
        public function currentSessions(): int
        {
            $Query = "SELECT sessions FROM `vpns`";
            $QueryResults = $this->openBlu->database->query($Query);

            if($QueryResults == false)
            {
                return 0;
            }

            $Results = 0;

            while($Row = $QueryResults->fetch_array())
            {
                $Results += (int)$Row['sessions'];
            }

            return $Results;
        }

        /**
         * Returns the total amount of sessions in total
         *
         * @return int
         */
        public function totalSessions(): int
        {
            $Query = "SELECT total_sessions FROM `vpns`";
            $QueryResults = $this->openBlu->database->query($Query);

            if($QueryResults == false)
            {
                return 0;
            }

            $Results = 0;

            while($Row = $QueryResults->fetch_array())
            {
                $Results += (int)$Row['total_sessions'];
            }

            return $Results;
        }

        /**
         * Returns the total amount of pages that are available
         *
         * @return int
         * @throws DatabaseException
         */
        public function totalServerPages(): int
        {
            $Query = "SELECT id FROM `vpns`";
            $QueryResults = $this->openBlu->database->query($Query);

            if($QueryResults == false)
            {
                throw new DatabaseException($this->openBlu->database->error, $Query);
            }
            else
            {
                if($QueryResults->num_rows == 0)
                {
                    return 0;
                }
                else
                {
                    return ceil($QueryResults->num_rows / 40);
                }
            }
        }

        /**
         * Returns the contents of the query page
         *
         * @param int $page
         * @return array
         * @throws DatabaseException
         * @throws PageNotFoundException
         */
        public function getServerPage(int $page): array
        {
            $TotalPages = $this->totalServerPages();

            if($page > $TotalPages)
            {
                throw new PageNotFoundException();
            }

            if($page < 1)
            {
                throw new PageNotFoundException();
            }

            $Query = null;
            if($page == 1)
            {
                $Query = "SELECT id, public_id, host_name, ip_address, score, ping, country, country_short, sessions, total_sessions, last_updated, created FROM `vpns` ORDER BY `sessions` DESC LIMIT 0, 40";
            }
            else
            {
                $CurrentPage = 0;
                $StartingItem = 0;

                while(true)
                {
                    $CurrentPage += 1;
                    $StartingItem += 40;
                    if($CurrentPage == $page - 1)
                    {
                        break;
                    }
                }

                $Query = "SELECT id, public_id, host_name, ip_address, score, ping, country, country_short, sessions, total_sessions, last_updated, created FROM `vpns` ORDER BY `sessions` DESC LIMIT $StartingItem, 40";
            }

            $QueryResults = $this->openBlu->database->query($Query);
            if($QueryResults == false)
            {
                throw new DatabaseException($this->openBlu->database->error, $Query);
            }
            else
            {
                $ResultsArray = [];

                while($Row = $QueryResults->fetch_assoc())
                {
                    $ResultsArray[] = $Row;
                }

                return $ResultsArray;
            }
        }

        /**
         * Returns the most popular 5 servers
         *
         * @return array
         * @throws DatabaseException
         */
        public function getPopularServers(): array
        {
            $Query = "SELECT id, public_id, host_name, ip_address, score, ping, country, country_short, sessions, total_sessions, last_updated, created FROM `vpns` ORDER BY  `sessions` DESC LIMIT 0, 5";
            $QueryResults = $this->openBlu->database->query($Query);

            if($QueryResults == false)
            {
                throw new DatabaseException($this->openBlu->database->error, $Query);
            }
            else
            {
                $ResultsArray = [];

                while($Row = $QueryResults->fetch_assoc())
                {
                    $ResultsArray[] = $Row;
                }

                return $ResultsArray;
            }
        }
    }