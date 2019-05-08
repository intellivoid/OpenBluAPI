<?php


    namespace OpenBlu\Managers;

    use ModularAPI\Abstracts\AccessKeySearchMethod;
    use ModularAPI\Abstracts\AccessKeyStatus;
    use ModularAPI\Configurations\PermissionsConfiguration;
    use ModularAPI\Configurations\UsageConfiguration;
    use ModularAPI\Exceptions\AccessKeyNotFoundException;
    use ModularAPI\Exceptions\InvalidAccessKeyStatusException;
    use ModularAPI\Exceptions\NoResultsFoundException;
    use ModularAPI\Exceptions\UnsupportedSearchMethodException;
    use ModularAPI\ModularAPI;
    use ModularAPI\Objects\AccessKey;
    use OpenBlu\Abstracts\BillingCycle;
    use OpenBlu\Exceptions\ConfigurationNotFoundException;
    use OpenBlu\OpenBlu;

    /**
     * Class APIManager
     * @package OpenBlu\Managers
     */
    class APIManager
    {
        /**
         * @var ModularAPI
         */
        private $modularAPI;

        /**
         * @var OpenBlu
         */
        private $openBlu;

        /**
         * APIManager constructor.
         * @param OpenBlu $openBlu
         */
        public function __construct(OpenBlu $openBlu)
        {
           $this->openBlu = $openBlu;
        }

        /**
         * @param int $monthlyCalls
         * @param BillingCycle|int $billingCycle
         * @return AccessKey
         * @throws InvalidAccessKeyStatusException
         * @throws NoResultsFoundException
         * @throws UnsupportedSearchMethodException
         */
        public function createKey(int $monthlyCalls, int $billingCycle): AccessKey
        {
            if($this->modularAPI == null) { $this->modularAPI = new ModularAPI(true); }


            return $this->modularAPI->AccessKeys()->createKey(
                UsageConfiguration::dateIntervalLimit($monthlyCalls, $billingCycle),
                PermissionsConfiguration::specifyPermissions(array(
                    'test'
                ))
            );
        }

        /**
         * @param int $publicId
         * @throws AccessKeyNotFoundException
         * @throws NoResultsFoundException
         * @throws UnsupportedSearchMethodException
         */
        public function suspendKey(int $publicId)
        {
            if($this->modularAPI == null) { $this->modularAPI = new ModularAPI(true); }

            $accessKey = $this->modularAPI->AccessKeys()->Manager->get(AccessKeySearchMethod::byPublicID, $publicId);
            $accessKey->State = AccessKeyStatus::Suspended;
            $this->modularAPI->AccessKeys()->Manager->update($accessKey);
        }
    }