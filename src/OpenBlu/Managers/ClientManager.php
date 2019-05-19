<?php


    namespace OpenBlu\Managers;


    use OpenBlu\Abstracts\SearchMethods\ClientSearchMethod;
    use OpenBlu\Exceptions\ClientNotFoundException;
    use OpenBlu\Exceptions\DatabaseException;
    use OpenBlu\Exceptions\InvalidClientPropertyException;
    use OpenBlu\Exceptions\InvalidIPAddressException;
    use OpenBlu\Exceptions\InvalidSearchMethodException;
    use OpenBlu\Objects\Client;
    use OpenBlu\OpenBlu;
    use OpenBlu\Utilities\Hashing;
    use OpenBlu\Utilities\Validate;

    class ClientManager
    {
        /**
         * @var OpenBlu
         */
        private $openBlu;

        /**
         * ClientManager constructor.
         * @param OpenBlu $openBlu
         */
        public function __construct(OpenBlu $openBlu)
        {
            $this->openBlu = $openBlu;
        }

        /**
         * Registers a new client into the database
         *
         * @param Client $client
         * @return Client
         * @throws InvalidClientPropertyException
         * @throws InvalidIPAddressException
         * @throws DatabaseException
         * @throws InvalidSearchMethodException
         * @throws ClientNotFoundException
         */
        public function registerClient(Client $client): Client
        {
            if(strlen($client->ClientName) > 100)
            {
                throw new InvalidClientPropertyException();
            }

            if(strlen($client->ClientVersion) > 15)
            {
                throw new InvalidClientPropertyException();
            }

            if(strlen($client->ClientUid) !== 64)
            {
                throw new InvalidClientPropertyException();
            }

            if(strlen($client->osName) > 126)
            {
                throw new InvalidClientPropertyException();
            }

            if(strlen($client->osVersion) > 64)
            {
                throw new InvalidClientPropertyException();
            }

            if(Validate::IP($client->ipAddress) == false)
            {
                throw new InvalidIPAddressException();
            }

            // TODO: Determine if the client UID already exists

            $RegisteredTimestamp = time();
            $LastConnectedTimestamp = $RegisteredTimestamp;
            $PublicID = Hashing::calculateClientPublicID($client->ClientUid, $client->ClientName, $RegisteredTimestamp);
            $PublicID = $this->openBlu->database->real_escape_string($PublicID);
            $AccountID = 0;
            $AuthExpires = 0;
            $ClientName = $this->openBlu->database->real_escape_string($client->ClientName);
            $ClientVersion = $this->openBlu->database->real_escape_string($client->ClientVersion);
            $ClientUid = $this->openBlu->database->real_escape_string($client->ClientUid);
            $osName = $this->openBlu->database->real_escape_string($client->osName);
            $osVersion = $this->openBlu->database->real_escape_string($client->osVersion);
            $ipAddress = $this->openBlu->database->real_escape_string($client->ipAddress);
            $blocked = (int)false;

            $Query = sprintf("INSERT INTO `clients` (public_id, account_id, auth_expires, client_name, client_version, client_uid, os_name, os_version, ip_address, blocked, last_connected_timestamp, registered_timestamp) VALUES ('%s', %s, %s, '%s', '%s', '%s', '%s', '%s', '%s', %s, %s, %s)", $PublicID, $AccountID, $AuthExpires, $ClientName, $ClientVersion, $ClientUid, $osName, $osVersion, $ipAddress, $blocked, $LastConnectedTimestamp, $RegisteredTimestamp);
            $QueryResults = $this->openBlu->database->query($Query);

            if($QueryResults == true)
            {
                return $this->getClient(ClientSearchMethod::byPublicId, $PublicID);
            }
            else
            {
                throw new DatabaseException($this->openBlu->database->error, $Query);
            }

        }

        /**
         * @param ClientSearchMethod|string $search_method
         * @param string $value
         * @return Client
         * @throws InvalidSearchMethodException
         * @throws DatabaseException
         * @throws ClientNotFoundException
         */
        public function getClient(string $search_method, string $value): Client
        {
            switch($search_method)
            {
                case ClientSearchMethod::byId:
                    $search_method = $this->openBlu->database->real_escape_string($search_method);
                    $value = (int)$value;
                    break;

                case ClientSearchMethod::byPublicId:
                    $search_method = $this->openBlu->database->real_escape_string($search_method);
                    $value = "'" . $this->openBlu->database->real_escape_string($value) . "'";
                    break;

                case ClientSearchMethod::byClientUid:
                    $search_method = $this->openBlu->database->real_escape_string($search_method);
                    $value = "'" . $this->openBlu->database->real_escape_string($value) . "'";
                    break;

                default:
                    throw new InvalidSearchMethodException();
            }

            $Query = "SELECT id, public_id, account_id, auth_expires, client_name, client_version, client_uid, os_name, os_version, ip_address, blocked, last_connected_timestamp, registered_timestamp FROM `clients` WHERE $search_method=$value";
            $QueryResults = $this->openBlu->database->query($Query);

            if($QueryResults == false)
            {
                throw new DatabaseException($this->openBlu->database->error, $Query);
            }
            else
            {
                if ($QueryResults->num_rows !== 1)
                {
                    throw new ClientNotFoundException();
                }

                return Client::fromArray($QueryResults->fetch_array(MYSQLI_ASSOC));
            }
        }

        /**
         * @param Client $client
         * @return bool
         * @throws DatabaseException
         * @throws InvalidSearchMethodException
         * @throws ClientNotFoundException
         */
        public function updateClient(Client $client): bool
        {
            $this->getClient(ClientSearchMethod::byId, $client->ID);

            $ID = (int)$client->ID;
            $PublicID = $this->openBlu->database->real_escape_string($client->PublicID);
            $AccountID = (int)$client->AccountID;
            $AuthExpires = (int)$client->AuthExpires;
            $ClientName = $this->openBlu->database->real_escape_string($client->ClientName);
            $ClientVersion = $this->openBlu->database->real_escape_string($client->ClientVersion);
            $ClientUid = $this->openBlu->database->real_escape_string($client->ClientUid);
            $osName = $this->openBlu->database->real_escape_string($client->osName);
            $osVersion = $this->openBlu->database->real_escape_string($client->osVersion);
            $ipAddress = $this->openBlu->database->real_escape_string($client->ipAddress);
            $blocked = (int)$client->Blocked;
            $last_connected_timestamp = (int)$client->LastConnectedTimestamp;
            $registered_timestamp = (int)$client->RegisteredTimestamp;

            $Query = "UPDATE `clients` SET public_id='$PublicID', account_id=$AccountID, auth_expires=$AuthExpires, client_name='$ClientName', client_version='$ClientVersion', client_uid='$ClientUid', os_name='$osName', os_version='$osVersion', ip_address='$ipAddress', blocked=$blocked, last_connected_timestamp=$last_connected_timestamp, registered_timestamp=$registered_timestamp WHERE id=$ID";
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
         * Determines if the client ID exists in the database
         *
         * @param int $id
         * @return bool
         * @throws DatabaseException
         * @throws InvalidSearchMethodException
         */
        public function idExists(int $id): bool
        {
            try
            {
                $this->getClient(ClientSearchMethod::byId, $id);
                return true;
            }
            catch(ClientNotFoundException $clientNotFoundException)
            {
                return false;
            }
        }

        /**
         * Determines if the Public ID exists in the database
         *
         * @param string $public_id
         * @return bool
         * @throws DatabaseException
         * @throws InvalidSearchMethodException
         */
        public function publicIdExists(string $public_id): bool
        {
            try
            {
                $this->getClient(ClientSearchMethod::byPublicId, $public_id);
                return true;
            }
            catch(ClientNotFoundException $clientNotFoundException)
            {
                return false;
            }
        }

        /**
         * Determines if the client has already been registered into the database
         *
         * @param string $client_uid
         * @return bool
         * @throws DatabaseException
         * @throws InvalidSearchMethodException
         */
        public function clientUidExists(string $client_uid): bool
        {
            try
            {
                $this->getClient(ClientSearchMethod::byClientUid, $client_uid);
                return true;
            }
            catch(ClientNotFoundException $clientNotFoundException)
            {
                return false;
            }
        }
    }