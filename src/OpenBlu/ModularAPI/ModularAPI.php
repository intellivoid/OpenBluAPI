<?php

    namespace ModularAPI;

    use ModularAPI\DatabaseManager\Requests;
    use ModularAPI\Managers\AccessKeyManager;
    use mysqli;

    define('MODULAR_API', __DIR__ . DIRECTORY_SEPARATOR);

    include_once(MODULAR_API . 'Abstracts' . DIRECTORY_SEPARATOR . 'HTTP' . DIRECTORY_SEPARATOR . 'ResponseCode' . DIRECTORY_SEPARATOR . 'ClientError.php');
    include_once(MODULAR_API . 'Abstracts' . DIRECTORY_SEPARATOR . 'HTTP' . DIRECTORY_SEPARATOR . 'ResponseCode' . DIRECTORY_SEPARATOR . 'Information.php');
    include_once(MODULAR_API . 'Abstracts' . DIRECTORY_SEPARATOR . 'HTTP' . DIRECTORY_SEPARATOR . 'ResponseCode' . DIRECTORY_SEPARATOR . 'Redirect.php');
    include_once(MODULAR_API . 'Abstracts' . DIRECTORY_SEPARATOR . 'HTTP' . DIRECTORY_SEPARATOR . 'ResponseCode' . DIRECTORY_SEPARATOR . 'ServerError.php');
    include_once(MODULAR_API . 'Abstracts' . DIRECTORY_SEPARATOR . 'HTTP' . DIRECTORY_SEPARATOR . 'ResponseCode' . DIRECTORY_SEPARATOR . 'Successful.php');
    include_once(MODULAR_API . 'Abstracts' . DIRECTORY_SEPARATOR . 'HTTP' . DIRECTORY_SEPARATOR . 'ContentType.php');
    include_once(MODULAR_API . 'Abstracts' . DIRECTORY_SEPARATOR . 'HTTP' . DIRECTORY_SEPARATOR . 'FileType.php');
    include_once(MODULAR_API . 'Abstracts' . DIRECTORY_SEPARATOR . 'HTTP' . DIRECTORY_SEPARATOR . 'RequestMethod.php');
    include_once(MODULAR_API . 'Abstracts' . DIRECTORY_SEPARATOR . 'AccessKeySearchMethod.php');
    include_once(MODULAR_API . 'Abstracts' . DIRECTORY_SEPARATOR . 'AccessKeyStatus.php');
    include_once(MODULAR_API . 'Abstracts' . DIRECTORY_SEPARATOR . 'AuthenticationType.php');
    include_once(MODULAR_API . 'Abstracts' . DIRECTORY_SEPARATOR . 'ExceptionCodes.php');
    include_once(MODULAR_API . 'Abstracts' . DIRECTORY_SEPARATOR . 'UsageType.php');
    include_once(MODULAR_API . 'Configurations' . DIRECTORY_SEPARATOR . 'PermissionsConfiguration.php');
    include_once(MODULAR_API . 'Configurations' . DIRECTORY_SEPARATOR . 'UsageConfiguration.php');
    include_once(MODULAR_API . 'DatabaseManager' . DIRECTORY_SEPARATOR . 'AccessKeys.php');
    include_once(MODULAR_API . 'DatabaseManager' . DIRECTORY_SEPARATOR . 'Requests.php');
    include_once(MODULAR_API . 'Exceptions' . DIRECTORY_SEPARATOR . 'AccessKeyExpiredException.php');
    include_once(MODULAR_API . 'Exceptions' . DIRECTORY_SEPARATOR . 'AccessKeyNotFoundException.php');
    include_once(MODULAR_API . 'Exceptions' . DIRECTORY_SEPARATOR . 'DatabaseException.php');
    include_once(MODULAR_API . 'Exceptions' . DIRECTORY_SEPARATOR . 'DatabaseNotEstablishedException.php');
    include_once(MODULAR_API . 'Exceptions' . DIRECTORY_SEPARATOR . 'InvalidAccessKeyStatusException.php');
    include_once(MODULAR_API . 'Exceptions' . DIRECTORY_SEPARATOR . 'InvalidRequestQueryException.php');
    include_once(MODULAR_API . 'Exceptions' . DIRECTORY_SEPARATOR . 'MissingParameterException.php');
    include_once(MODULAR_API . 'Exceptions' . DIRECTORY_SEPARATOR . 'NoResultsFoundException.php');
    include_once(MODULAR_API . 'Exceptions' . DIRECTORY_SEPARATOR . 'UnsupportedClientException.php');
    include_once(MODULAR_API . 'Exceptions' . DIRECTORY_SEPARATOR . 'UnsupportedSearchMethodException.php');
    include_once(MODULAR_API . 'Exceptions' . DIRECTORY_SEPARATOR . 'UsageExceededException.php');
    include_once(MODULAR_API . 'HTTP' . DIRECTORY_SEPARATOR . 'Headers.php');
    include_once(MODULAR_API . 'HTTP' . DIRECTORY_SEPARATOR . 'Request.php');
    include_once(MODULAR_API . 'HTTP' . DIRECTORY_SEPARATOR . 'Response.php');
    include_once(MODULAR_API . 'Managers' . DIRECTORY_SEPARATOR . 'AccessKeyManager.php');
    include_once(MODULAR_API . 'Objects' . DIRECTORY_SEPARATOR . 'AccessKey' . DIRECTORY_SEPARATOR . 'Analytics.php');
    include_once(MODULAR_API . 'Objects' . DIRECTORY_SEPARATOR . 'AccessKey' . DIRECTORY_SEPARATOR . 'Permissions.php');
    include_once(MODULAR_API . 'Objects' . DIRECTORY_SEPARATOR . 'AccessKey' . DIRECTORY_SEPARATOR . 'Signatures.php');
    include_once(MODULAR_API . 'Objects' . DIRECTORY_SEPARATOR . 'AccessKey' . DIRECTORY_SEPARATOR . 'Usage.php');
    include_once(MODULAR_API . 'Objects' . DIRECTORY_SEPARATOR . 'AccessKey.php');
    include_once(MODULAR_API . 'Objects' . DIRECTORY_SEPARATOR . 'API.php');
    include_once(MODULAR_API . 'Objects' . DIRECTORY_SEPARATOR . 'Configuration.php');
    include_once(MODULAR_API . 'Objects' . DIRECTORY_SEPARATOR . 'ExceptionDetails.php');
    include_once(MODULAR_API . 'Objects' . DIRECTORY_SEPARATOR . 'Module.php');
    include_once(MODULAR_API . 'Objects' . DIRECTORY_SEPARATOR . 'Parameter.php');
    include_once(MODULAR_API . 'Objects' . DIRECTORY_SEPARATOR . 'Policies.php');
    include_once(MODULAR_API . 'Objects' . DIRECTORY_SEPARATOR . 'RequestAuthentication.php');
    include_once(MODULAR_API . 'Objects' . DIRECTORY_SEPARATOR . 'RequestQuery.php');
    include_once(MODULAR_API . 'Objects' . DIRECTORY_SEPARATOR . 'RequestRecord.php');
    include_once(MODULAR_API . 'Objects' . DIRECTORY_SEPARATOR . 'Response.php');
    include_once(MODULAR_API . 'Utilities' . DIRECTORY_SEPARATOR . 'Builder.php');
    include_once(MODULAR_API . 'Utilities' . DIRECTORY_SEPARATOR . 'Checker.php');
    include_once(MODULAR_API . 'Utilities' . DIRECTORY_SEPARATOR . 'Hashing.php');

    /**
     * Main AutoLoader for ModularAPI
     *
     * Class ModularAPI
     * @package ModularAPI
     */
    class ModularAPI
    {
        /**
         * The Database connection, null if Database connection isn't established
         *
         * @var null|mysqli
         */
        public $Database;

        /**
         * @var AccessKeyManager
         */
        private $AccessKeyManager;

        /**
         * @var DatabaseManager\Requests
         */
        private $RequestsLog;

        /**
         * Constructs ModularAPI Library
         *
         * ModularAPI constructor.
         * @param bool $EstablishDatabaseConnection
         */
        public function __construct(bool $EstablishDatabaseConnection = true)
        {
            $Configuration = self::getConfiguration();

            if($EstablishDatabaseConnection == true)
            {
                $this->Database = new mysqli(
                    $Configuration['ModularAPI_DatabaseHost'],
                    $Configuration['ModularAPI_DatabaseUsername'],
                    $Configuration['ModularAPI_DatabasePassword'],
                    $Configuration['ModularAPI_DatabaseName'],
                    $Configuration['ModularAPI_DatabasePort']
                );
            }
            else
            {
                $this->Database = null;
            }

            $this->AccessKeyManager = new AccessKeyManager($this);
            $this->RequestsLog = new DatabaseManager\Requests($this);
        }

        /**
         * Manages Access Keys
         *
         * @return AccessKeyManager
         */
        public function AccessKeys(): AccessKeyManager
        {
            return $this->AccessKeyManager;
        }

        /**
         * Manages Request Logs
         *
         * @return Requests
         */
        public function RequestsLog(): Requests
        {
            return $this->RequestsLog;
        }

        /**
         * Get configuration
         *
         * @return array
         */
        public static function getConfiguration(): array
        {
            return parse_ini_file(__DIR__ . DIRECTORY_SEPARATOR . 'configuration.ini', false);
        }
    }