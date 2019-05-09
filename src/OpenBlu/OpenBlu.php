<?php

    namespace OpenBlu;

    use AnalyticsManager\AnalyticsManager;
    use mysqli;
    use OpenBlu\Exceptions\ConfigurationNotFoundException;
    use OpenBlu\Managers\APIManager;
    use OpenBlu\Managers\ClientManager;
    use OpenBlu\Managers\PlanManager;
    use OpenBlu\Managers\RecordManager;
    use OpenBlu\Managers\VPNManager;

    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Abstracts' . DIRECTORY_SEPARATOR . 'SearchMethods' . DIRECTORY_SEPARATOR . 'ClientSearchMethod.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Abstracts' . DIRECTORY_SEPARATOR . 'SearchMethods' . DIRECTORY_SEPARATOR . 'PlanSearchMethod.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Abstracts' . DIRECTORY_SEPARATOR . 'SearchMethods' . DIRECTORY_SEPARATOR . 'UpdateRecord.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Abstracts' . DIRECTORY_SEPARATOR . 'SearchMethods' . DIRECTORY_SEPARATOR . 'VPN.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Abstracts' . DIRECTORY_SEPARATOR . 'APIPlan.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Abstracts' . DIRECTORY_SEPARATOR . 'BillingCycle.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Abstracts' . DIRECTORY_SEPARATOR . 'DefaultValues.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Abstracts' . DIRECTORY_SEPARATOR . 'ExceptionCodes.php');

    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Exceptions' . DIRECTORY_SEPARATOR . 'ClientNotFoundException.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Exceptions' . DIRECTORY_SEPARATOR . 'ConfigurationNotFoundException.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Exceptions' . DIRECTORY_SEPARATOR . 'DatabaseException.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Exceptions' . DIRECTORY_SEPARATOR . 'InvalidApiPlanTypeException.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Exceptions' . DIRECTORY_SEPARATOR . 'InvalidClientPropertyException.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Exceptions' . DIRECTORY_SEPARATOR . 'InvalidIPAddressException.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Exceptions' . DIRECTORY_SEPARATOR . 'InvalidSearchMethodException.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Exceptions' . DIRECTORY_SEPARATOR . 'PageNotFoundException.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Exceptions' . DIRECTORY_SEPARATOR . 'PlanNotFoundException.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Exceptions' . DIRECTORY_SEPARATOR . 'SyncException.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Exceptions' . DIRECTORY_SEPARATOR . 'UpdateRecordNotFoundException.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Exceptions' . DIRECTORY_SEPARATOR . 'VPNNotFoundException.php');

    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Managers' . DIRECTORY_SEPARATOR . 'APIManager.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Managers' . DIRECTORY_SEPARATOR . 'ClientManager.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Managers' . DIRECTORY_SEPARATOR . 'PlanManager.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Managers' . DIRECTORY_SEPARATOR . 'RecordManager.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Managers' . DIRECTORY_SEPARATOR . 'VPNManager.php');

    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Objects' . DIRECTORY_SEPARATOR . 'Client.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Objects' . DIRECTORY_SEPARATOR . 'Plan.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Objects' . DIRECTORY_SEPARATOR . 'UpdateRecord.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Objects' . DIRECTORY_SEPARATOR . 'VPN.php');

    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Utilities' . DIRECTORY_SEPARATOR . 'Corrector.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Utilities' . DIRECTORY_SEPARATOR . 'Hashing.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Utilities' . DIRECTORY_SEPARATOR . 'OpenVPNConfiguration.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Utilities' . DIRECTORY_SEPARATOR . 'Validate.php');

    if(class_exists('AnalyticsManager\AnalyticsManager') == false)
    {
        include_once(__DIR__ . DIRECTORY_SEPARATOR . 'AnalyticsManager' . DIRECTORY_SEPARATOR . 'AnalyticsManager.php');
    }

    if(class_exists('ModularAPI\ModularAPI') == false)
    {
        include_once(__DIR__ . DIRECTORY_SEPARATOR . 'ModularAPI' . DIRECTORY_SEPARATOR . 'ModularAPI.php');
    }

    /**
     * Class OpenBlu
     * @package OpenBlu
     */
    class OpenBlu
    {
        /**
         * @var array|bool
         */
        public $configuration;

        /**
         * @var mysqli
         */
        public $database;

        /**
         * @var RecordManager
         */
        private $RecordManager;

        /**
         * @var VPNManager
         */
        private $VPNManager;

        /**
         * @var AnalyticsManager
         */
        private $AnalyticsManager;

        /**
         * @var APIManager
         */
        private $APIManager;
        
        /**
         * @var PlanManager
         */
        private $PlanManager;

        /**
         * @var ClientManager
         */
        private $ClientManager;

        /**
         * OpenBlu constructor.
         * @throws ConfigurationNotFoundException
         */
        public function __construct()
        {
            if(file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'configuration.ini') == false)
            {
                throw new ConfigurationNotFoundException();
            }

            $this->configuration = parse_ini_file(__DIR__ . DIRECTORY_SEPARATOR . 'configuration.ini');

            $this->database = new mysqli(
                $this->configuration['DatabaseHost'],
                $this->configuration['DatabaseUsername'],
                $this->configuration['DatabasePassword'],
                $this->configuration['DatabaseName'],
                $this->configuration['DatabasePort']
            );

            $this->RecordManager = new RecordManager($this);
            $this->VPNManager = new VPNManager($this);
            $this->AnalyticsManager = new AnalyticsManager($this->configuration['DatabaseName']);
            $this->APIManager = new APIManager($this);
            $this->PlanManager = new PlanManager($this);
            $this->ClientManager = new ClientManager($this);
        }

        /**
         * @return RecordManager
         */
        public function getRecordManager(): RecordManager
        {
            return $this->RecordManager;
        }

        /**
         * @return VPNManager
         */
        public function getVPNManager(): VPNManager
        {
            return $this->VPNManager;
        }

        /**
         * @return AnalyticsManager
         */
        public function getAnalyticsManager(): AnalyticsManager
        {
            return $this->AnalyticsManager;
        }

        /**
         * @param string $file
         * @return string
         */
        public static function getResource(string $file): string
        {
            return(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'Resources' . DIRECTORY_SEPARATOR . $file));
        }

        /**
         * @return APIManager
         */
        public function getAPIManager(): APIManager
        {
            return $this->APIManager;
        }

        /**
         * @return PlanManager
         */
        public function getPlanManager(): PlanManager
        {
            return $this->PlanManager;
        }

        /**
         * @return ClientManager
         */
        public function getClientManager(): ClientManager
        {
            return $this->ClientManager;
        }
    }