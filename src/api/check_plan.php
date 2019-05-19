<?php

    use IntellivoidAccounts\Abstracts\SearchMethods\AccountSearchMethod;
    use IntellivoidAccounts\Exceptions\AccountNotFoundException;
    use IntellivoidAccounts\Exceptions\InvalidAccountStatusException;
    use IntellivoidAccounts\Exceptions\InvalidEmailException;
    use IntellivoidAccounts\Exceptions\InvalidPasswordException;
    use IntellivoidAccounts\Exceptions\InvalidUsernameException;
    use IntellivoidAccounts\IntellivoidAccounts;
    use ModularAPI\Abstracts\HTTP\ContentType;
    use ModularAPI\Abstracts\HTTP\FileType;
    use ModularAPI\Abstracts\HTTP\ResponseCode\ClientError;
    use ModularAPI\Exceptions\AccessKeyNotFoundException;
    use ModularAPI\Exceptions\NoResultsFoundException;
    use ModularAPI\Exceptions\UnsupportedSearchMethodException;
    use ModularAPI\Objects\AccessKey;
    use ModularAPI\Objects\Response;
    use OpenBlu\Abstracts\SearchMethods\PlanSearchMethod;
    use OpenBlu\Exceptions\ConfigurationNotFoundException;
    use OpenBlu\Exceptions\DatabaseException;
    use OpenBlu\Exceptions\InvalidSearchMethodException;
    use OpenBlu\Exceptions\PlanNotFoundException;
    use OpenBlu\Exceptions\UpdateRecordNotFoundException;
    use OpenBlu\OpenBlu;

    /**
     * Written for the OpenBlu API, this script checks if the plan
     * is still active, if not then it will charge the account.
     */


    /**
     * Checks if the plan is requiring payment, if it does processes the transaction
     * and continues.
     *
     * @param AccessKey $accessKey
     * @return Response|null
     * @throws AccountNotFoundException
     * @throws \IntellivoidAccounts\Exceptions\ConfigurationNotFoundException
     * @throws \IntellivoidAccounts\Exceptions\DatabaseException
     * @throws InvalidAccountStatusException
     * @throws InvalidEmailException
     * @throws InvalidPasswordException
     * @throws \IntellivoidAccounts\Exceptions\InvalidSearchMethodException
     * @throws InvalidUsernameException
     * @throws AccessKeyNotFoundException
     * @throws NoResultsFoundException
     * @throws UnsupportedSearchMethodException
     * @throws ConfigurationNotFoundException
     * @throws DatabaseException
     * @throws InvalidSearchMethodException
     * @throws PlanNotFoundException
     * @throws UpdateRecordNotFoundException
     */
    function checkPlan(AccessKey $accessKey)
    {
        $OpenBlu = new OpenBlu();

        $Plan = $OpenBlu->getPlanManager()->getPlan(PlanSearchMethod::byAccessKeyId, $accessKey->ID);

        if($Plan->Active == false)
        {
            if($Plan->PaymentRequired == false)
            {
                $Response = new Response();
                $Response->ResponseCode = ClientError::_400;
                $Response->ResponseType = ContentType::application . '/' . FileType::json;
                $Response->Content = array(
                    'status' => false,
                    'code' => ClientError::_400,
                    'message' => 'API Plan is not active, see dashboard for more information'
                );

                return $Response;
            }
        }

        if(time() > $Plan->NextBillingCycle)
        {
            $IntellivoidAccounts = new IntellivoidAccounts();
            $Account = $IntellivoidAccounts->getAccountManager()->getAccount(AccountSearchMethod::byId, $Plan->AccountId);

            if($Plan->PricePerCycle > $Account->Configuration->Balance)
            {
                $Plan->Active = false;
                $Plan->PaymentRequired = true;

                $OpenBlu->getPlanManager()->updatePlan($Plan);

                $Response = new Response();
                $Response->ResponseCode = ClientError::_400;
                $Response->ResponseType = ContentType::application . '/' . FileType::json;
                $Response->Content = array(
                    'status' => false,
                    'code' => ClientError::_400,
                    'message' => 'Insufficient funds for API Billing Cycle'
                );

                return $Response;
            }

            $Account->Configuration->Balance = $Account->Configuration->Balance - $Plan->PricePerCycle;
            $Plan->NextBillingCycle = time() + $Plan->BillingCycle;
            $Plan->Active = true;
            $Plan->PaymentRequired = false;


            $IntellivoidAccounts->getAccountManager()->updateAccount($Account);
            $OpenBlu->getPlanManager()->updatePlan($Plan);
        }

        return null;
    }