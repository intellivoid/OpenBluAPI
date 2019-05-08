<?php


    namespace OpenBlu\Objects;


    /**
     * Class Client
     * @package OpenBlu\Objects
     */
    class Client
    {
        /**
         * The internal private ID associated with the database
         *
         * @var int
         */
        public $ID;

        /**
         * The Public ID of this client
         *
         * @var string
         */
        public $PublicID;

        /**
         * The account ID that this client is associated with
         *
         * @var int
         */
        public $AccountID;

        /**
         * The Unix Timestamp of when the current auth session expires
         *
         * @var int
         */
        public $AuthExpires;

        /**
         * The name of the client
         *
         * @var string
         */
        public $ClientName;

        /**
         * The version of the client
         *
         * @var string
         */
        public $ClientVersion;

        /**
         * The unique ID given by the client
         *
         * @var string
         */
        public $ClientUid;

        /**
         * The name of the operating system
         *
         * @var string
         */
        public $osName;

        /**
         * The version of the operating system
         *
         * @var string
         */
        public $osVersion;

        /**
         * The IP Address associated with this client
         *
         * @var string
         */
        public $ipAddress;

        /**
         * Indicates if this client has been blocked
         *
         * @var bool
         */
        public $Blocked;

        /**
         * The Unix Timestamp of when the client has last connected
         *
         * @var int
         */
        public $LastConnectedTimestamp;

        /**
         * The Unix Timestamp of when this client registered into the database
         *
         * @var int
         */
        public $RegisteredTimestamp;

        /**
         * Determines if this client is authorized
         *
         * @return bool
         */
        public function isAuthorized(): bool
        {
            if($this->AccountID == 0)
            {
                return false;
            }

            if(time() > $this->AuthExpires)
            {
                return false;
            }

            return true;
        }

        /**
         * Converts object to an array
         *
         * @return array
         */
        public function toArray(): array
        {
            return array(
                'id' => (int)$this->ID,
                'public_id' => $this->PublicID,
                'account_id' => (int)$this->AccountID,
                'auth_expires' => (int)$this->AuthExpires,
                'client_name' => $this->ClientName,
                'client_version' => $this->ClientVersion,
                'client_uid' => $this->ClientUid,
                'os_name' => $this->osName,
                'os_version' => $this->osVersion,
                'ip_address' => $this->ipAddress,
                'blocked' => (bool)$this->Blocked,
                'last_connected_timestamp' => (int)$this->LastConnectedTimestamp,
                'registered_timestamp' => (int)$this->RegisteredTimestamp
            );
        }

        /**
         * Creates an object from array
         *
         * @param array $data
         * @return Client
         */
        public static function fromArray(array $data): Client
        {
            $ClientObject = new Client();

            if(isset($data['id']))
            {
                $ClientObject->ID = (int)$data['id'];
            }

            if(isset($data['public_id']))
            {
                $ClientObject->PublicID = $data['public_id'];
            }

            if(isset($data['account_id']))
            {
                $ClientObject->AccountID = (int)$data['account_id'];
            }

            if(isset($data['auth_expires']))
            {
                $ClientObject->AuthExpires = (int)$data['auth_expires'];
            }

            if(isset($data['client_name']))
            {
                $ClientObject->ClientName = $data['client_name'];
            }

            if(isset($data['client_version']))
            {
                $ClientObject->ClientVersion = $data['client_version'];
            }

            if(isset($data['client_uid']))
            {
                $ClientObject->ClientUid = $data['client_uid'];
            }

            if(isset($data['os_name']))
            {
                $ClientObject->osName = $data['os_name'];
            }

            if(isset($data['os_version']))
            {
                $ClientObject->osVersion = $data['os_version'];
            }

            if(isset($data['ip_address']))
            {
                $ClientObject->ipAddress = $data['ip_address'];
            }

            if(isset($data['blocked']))
            {
                $ClientObject->Blocked = (bool)$data['blocked'];
            }

            if(isset($data['last_connected_timestamp']))
            {
                $ClientObject->LastConnectedTimestamp = (int)$data['last_connected_timestamp'];
            }

            if(isset($data['registered_timestamp']))
            {
                $ClientObject->RegisteredTimestamp = (int)$data['registered_timestamp'];
            }

            return $ClientObject;
        }
    }