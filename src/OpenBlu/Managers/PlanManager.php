<?php

    namespace OpenBlu\Managers;

    use Exception;
    use ModularAPI\Abstracts\AccessKeySearchMethod;
    use ModularAPI\Abstracts\UsageType;
    use ModularAPI\Configurations\UsageConfiguration;
    use ModularAPI\Exceptions\AccessKeyNotFoundException;
    use ModularAPI\Exceptions\InvalidAccessKeyStatusException;
    use ModularAPI\Exceptions\NoResultsFoundException;
    use ModularAPI\Exceptions\UnsupportedSearchMethodException;
    use ModularAPI\ModularAPI;
    use ModularAPI\Objects\AccessKey;
    use ModularAPI\Utilities\Builder;
    use ModularAPI\Utilities\Hashing;
    use OpenBlu\Abstracts\APIPlan;
    use OpenBlu\Abstracts\SearchMethods\PlanSearchMethod;
    use OpenBlu\Exceptions\DatabaseException;
    use OpenBlu\Exceptions\InvalidApiPlanTypeException;
    use OpenBlu\Exceptions\InvalidSearchMethodException;
    use OpenBlu\Exceptions\PlanNotFoundException;
    use OpenBlu\Exceptions\UpdateRecordNotFoundException;
    use OpenBlu\Objects\Plan;
    use OpenBlu\OpenBlu;

    /**
     * Class PlanManager
     * @package OpenBlu\Managers
     */
    class PlanManager
    {
        /**
         * @var OpenBlu
         */
        private $openBlu;

        /**
         * @var ModularAPI
         */
        private $modularApi;

        /**
         * PlanManager constructor.
         * @param OpenBlu $openBlu
         */
        public function __construct(OpenBlu $openBlu)
        {
            $this->openBlu = $openBlu;
            $this->modularApi = new ModularAPI();
        }

        /**
         * @param Plan $plan
         * @return Plan
         * @throws DatabaseException
         * @throws InvalidAccessKeyStatusException
         * @throws InvalidSearchMethodException
         * @throws NoResultsFoundException
         * @throws UnsupportedSearchMethodException
         * @throws UpdateRecordNotFoundException
         */
        public function createPlan(Plan $plan): Plan
        {
            // Register the plan into the database
            $accessKeyId = (int)$plan->AccessKeyId;
            $accountId = (int)$plan->AccountId;
            $active = (int)$plan->Active;
            $billing_cycle = (int)$plan->BillingCycle;
            $monthly_calls = (int)$plan->MonthlyCalls;
            $next_billing_cycle = (int)$plan->NextBillingCycle;
            $payment_required = (int)$plan->PaymentRequired;
            $plan_created = (int)$plan->PlanCreated;
            $plan_started = (int)$plan->PlanStarted;
            $plan_type = (int)$plan->PlanType;
            $price_per_cycle = (float)$plan->PricePerCycle;
            $promotion_code = $this->openBlu->database->real_escape_string($plan->PromotionCode);

            $query = sprintf(
                "INSERT INTO `plans` (active, account_id, access_key_id, plan_type, promotion_code, monthly_calls, price_per_cycle, next_billing_cycle, billing_cycle, payment_required, plan_created, plan_started) VALUES (%s, %s, %s, %s, '%s', %s, %s, %s, %s, %s, %s, %s)",
                $active, $accountId, $accessKeyId, $plan_type, $promotion_code, $monthly_calls, $price_per_cycle, $next_billing_cycle, $billing_cycle, $payment_required, $plan_created, $plan_started
            );
            $query_results = $this->openBlu->database->query($query);

            if($query_results == true)
            {
                return $this->getPlan(PlanSearchMethod::byAccessKeyId, $accessKeyId);
            }
            else
            {
                throw new DatabaseException($this->openBlu->database->error, $query);
            }
        }

        /**
         * Gets an existing plan from the database
         *
         * @param string $search_method
         * @param string $value
         * @return Plan
         * @throws InvalidSearchMethodException
         * @throws UpdateRecordNotFoundException
         * @throws DatabaseException
         */
        public function getPlan(string $search_method, string $value): Plan
        {
            switch($search_method)
            {
                case PlanSearchMethod::byId:
                    $search_method = $this->openBlu->database->real_escape_string($search_method);
                    $value = (int)$value;
                    break;

                case PlanSearchMethod::byAccessKeyId:
                    $search_method = $this->openBlu->database->real_escape_string($search_method);
                    $value = (int)$this->openBlu->database->real_escape_string($value);
                    break;

                case PlanSearchMethod::byAccountId:
                    $search_method = $this->openBlu->database->real_escape_string($search_method);
                    $value = (int)$this->openBlu->database->real_escape_string($value);
                    break;

                default:

                    throw new InvalidSearchMethodException();
            }

            $Query = "SELECT id, active, account_id, access_key_id, plan_type, promotion_code, monthly_calls, price_per_cycle, next_billing_cycle, billing_cycle, payment_required, plan_created, plan_started FROM `plans` WHERE $search_method=$value";
            $QueryResults = $this->openBlu->database->query($Query);

            if($QueryResults == false)
            {
                throw new DatabaseException($this->openBlu->database->error, $Query);
            }
            else
            {
                if ($QueryResults->num_rows !== 1)
                {
                    throw new UpdateRecordNotFoundException();
                }

                return Plan::fromArray($QueryResults->fetch_array(MYSQLI_ASSOC));
            }
        }

        /**
         * Updates an existing plan on the database
         *
         * @param Plan $plan
         * @return bool
         * @throws DatabaseException
         * @throws NoResultsFoundException
         * @throws PlanNotFoundException
         * @throws UnsupportedSearchMethodException
         * @throws AccessKeyNotFoundException
         */
        function updatePlan(Plan $plan): bool
        {
            if($this->IdExists($plan->Id) == false)
            {
                throw new PlanNotFoundException();
            }

            $id = (int)$plan->Id;
            $active = (int)$plan->Active;
            $account_id = (int)$plan->AccountId;
            $access_key_id = (int)$plan->AccessKeyId;
            $plan_type = (int)$plan->PlanType;
            $promotion_code = $this->openBlu->database->real_escape_string($plan->PromotionCode);
            $monthly_calls = (int)$plan->MonthlyCalls;
            $price_per_cycle = (float)$plan->PricePerCycle;
            $next_billing_cycle = (int)$plan->NextBillingCycle;
            $billing_cycle = (int)$plan->BillingCycle;
            $payment_required = (int)$plan->PaymentRequired;
            $plan_started = (int)$plan->PlanStarted;

            $Query = "UPDATE `plans` SET active=$active, account_id=$account_id, access_key_id=$access_key_id, plan_type=$plan_type, promotion_code='$promotion_code', monthly_calls=$monthly_calls, price_per_cycle=$price_per_cycle, next_billing_cycle=$next_billing_cycle, billing_cycle=$billing_cycle, payment_required=$payment_required, plan_started=$plan_started WHERE id=$id";
            $QueryResults = $this->openBlu->database->query($Query);

            if($QueryResults == false)
            {
                throw new DatabaseException($this->openBlu->database->error, $Query);
            }

            $AccessKeyObject = $this->modularApi->AccessKeys()->Manager->get(AccessKeySearchMethod::byID, $plan->AccessKeyId);

            if($plan->MonthlyCalls == 0)
            {
                $AccessKeyObject->Usage->UsageType = UsageType::Unlimited;
                $AccessKeyObject->Usage->ResetInterval = 0;
                $AccessKeyObject->Usage->NextInterval = 0;
                $AccessKeyObject->Usage->Limit = 0;
            }
            else
            {
                $AccessKeyObject->Usage->UsageType = UsageType::DateIntervalLimit;
                $AccessKeyObject->Usage->ResetInterval = 2628002;
                $AccessKeyObject->Usage->NextInterval = 0;
                $AccessKeyObject->Usage->Limit = $plan->MonthlyCalls;
            }

            $this->modularApi->AccessKeys()->Manager->update($AccessKeyObject);

            return true;
        }

        /**
         * Determines if the plan ID Record exists in the database or not
         *
         * @param int $id
         * @return bool
         */
        function IdExists(int $id): bool
        {
            try
            {
                $this->getPlan(PlanSearchMethod::byId, $id);
                return true;
            }
            catch(Exception $exception)
            {
                return false;
            }
        }

        /**
         * Determines if the Access Key ID is associated with any plan
         *
         * @param int $accessKeyId
         * @return bool
         */
        function accessKeyIdExists(int $accessKeyId): bool
        {
            try
            {
                $this->getPlan(PlanSearchMethod::byAccessKeyId, $accessKeyId);
                return true;
            }
            catch(Exception $exception)
            {
                return false;
            }
        }

        /**
         * Determines if the Account ID if the associated with any plan
         *
         * @param int $accountId
         * @return bool
         */
        function accountIdExists(int $accountId): bool
        {
            try
            {
                $this->getPlan(PlanSearchMethod::byAccountId, $accountId);
                return true;
            }
            catch(Exception $exception)
            {
                return false;
            }
        }

        /**
         * @param Plan $plan
         * @return AccessKey
         * @throws NoResultsFoundException
         * @throws UnsupportedSearchMethodException
         * @throws AccessKeyNotFoundException
         */
        function updateSignatures(Plan $plan): AccessKey
        {
            $AccessKeyObject = $this->modularApi->AccessKeys()->Manager->get(AccessKeySearchMethod::byID, $plan->AccessKeyId);

            $CurrentTime = time();

            $AccessKeyObject->Signatures->PrivateSignature = Hashing::generatePrivateSignature(
                $AccessKeyObject->Signatures->TimeSignature,
                $AccessKeyObject->Signatures->IssuerName,
                $CurrentTime
            );

            $AccessKeyObject->Signatures->PublicSignature = Hashing::generatePublicSignature(
                $AccessKeyObject->Signatures->TimeSignature,
                $AccessKeyObject->Signatures->PrivateSignature
            );

            $AccessKeyObject->PublicKey = Hashing::calculatePublicKey($AccessKeyObject->Signatures->createCertificate());
            $this->modularApi->AccessKeys()->Manager->update($AccessKeyObject);

            return $AccessKeyObject;
        }

        /**
         * @param int $accountId
         * @param string $planType
         * @param int $monthlyCalls
         * @param int $billingCycle
         * @param float $price
         * @param string $promotion_code
         * @return Plan
         * @throws AccessKeyNotFoundException
         * @throws DatabaseException
         * @throws InvalidAccessKeyStatusException
         * @throws InvalidSearchMethodException
         * @throws NoResultsFoundException
         * @throws PlanNotFoundException
         * @throws UnsupportedSearchMethodException
         * @throws UpdateRecordNotFoundException
         * @throws InvalidApiPlanTypeException
         */
        function startPlan(int $accountId, string $planType, int $monthlyCalls, int $billingCycle, float $price, string $promotion_code = 'NORMAL'): Plan
        {
            $PlanExists = false;
            $Plan = new Plan();

            // If the plan exists, load it from the datbase
            if($this->accountIdExists($accountId) == true)
            {
                $Plan = $this->getPlan(PlanSearchMethod::byAccountId, $accountId);
                $PlanExists = true;
            }
            else
            {
                $Plan->AccountId = $accountId;
            }

            $current_time = time();

            // Set the properties
            $Plan->BillingCycle = $billingCycle;
            $Plan->PricePerCycle = $price;
            $Plan->MonthlyCalls = $monthlyCalls;
            $Plan->PromotionCode = $promotion_code;
            $Plan->Active = true;
            $Plan->PlanStarted = true;
            $Plan->PaymentRequired = false;
            $Plan->NextBillingCycle = $current_time + $Plan->BillingCycle;
            $Plan->PlanCreated = $current_time;
            $Plan->AccountId = $accountId;

            // Set the plan type
            switch($planType)
            {
                case APIPlan::Free:
                    $Plan->PlanType = APIPlan::Free;
                    break;

                case APIPlan::Basic:
                    $Plan->PlanType = APIPlan::Basic;
                    break;

                case APIPlan::Enterprise:
                    $Plan->PlanType = APIPlan::Enterprise;
                    break;

                default:
                    throw new InvalidApiPlanTypeException();
            }

            $AccessKey = null;

            // If the plan didn't exist, create a new access key
            if($PlanExists == false)
            {
                // If monthly calls is set to 0, create an unlimited key
                if($Plan->MonthlyCalls == 0)
                {
                    $AccessKey = $this->modularApi->AccessKeys()->createKey(
                        UsageConfiguration::unlimited(),
                        array(
                            'type' => 'allow_all_permissions'
                        )
                    );
                }
                // Else, create a normal key that resets each month
                else
                {
                    $AccessKey = $this->modularApi->AccessKeys()->createKey(
                        UsageConfiguration::dateIntervalLimit($Plan->MonthlyCalls, 2628002),
                        array(
                            'type' => 'allow_all_permissions'
                        )
                    );
                }

                $Plan->AccessKeyId = $AccessKey->ID;
            }
            else
            {
                $AccessKey = $this->modularApi->AccessKeys()->Manager->get(AccessKeySearchMethod::byID, $Plan->AccessKeyId);

                $AccessKey->Analytics->LastMonthAvailable = false;
                $AccessKey->Analytics->LastMonthID = null;
                $AccessKey->Analytics->LastMonthUsage = [];

                $AccessKey->Analytics->CurrentMonthAvailable = true;
                $AccessKey->Analytics->CurrentMonthID = Hashing::calculateMonthID((int)date('n'), (int)date('Y'));
                $AccessKey->Analytics->CurrentMonthUsage = Builder::createMonthArray();

                if($Plan->MonthlyCalls == 0)
                {
                    $AccessKey->Usage->loadConfiguration(
                        UsageConfiguration::unlimited()
                    );
                }
                else
                {
                    $AccessKey->Usage->loadConfiguration(
                        UsageConfiguration::dateIntervalLimit($Plan->MonthlyCalls, 2628002)
                    );
                }

                $this->modularApi->AccessKeys()->Manager->update($AccessKey);
            }

            $Plan->NextBillingCycle = time() + $Plan->BillingCycle;

            if($PlanExists == true)
            {
                $this->updatePlan($Plan);
                $this->updateSignatures($Plan);
                return $Plan;
            }

            return $this->createPlan($Plan);
        }

        /**
         * Cancels an existing plan
         *
         * @param int $account_id
         * @return bool
         * @throws AccessKeyNotFoundException
         * @throws DatabaseException
         * @throws InvalidSearchMethodException
         * @throws NoResultsFoundException
         * @throws PlanNotFoundException
         * @throws UnsupportedSearchMethodException
         * @throws UpdateRecordNotFoundException
         */
        function cancelPlan(int $account_id): bool
        {
            if($this->accountIdExists($account_id) == false)
            {
                return false;
            }

            $Plan = $this->getPlan(PlanSearchMethod::byAccountId, $account_id);

            $Plan->Active = false;
            $Plan->PlanStarted = false;
            $Plan->NextBillingCycle = 0;

            $this->updatePlan($Plan);

            return true;
        }

    }