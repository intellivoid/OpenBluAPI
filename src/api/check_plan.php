<?php

    use IntellivoidAccounts\Abstracts\TransactionType;
    use IntellivoidAccounts\Exceptions\InsufficientFundsException;
    use IntellivoidAccounts\IntellivoidAccounts;
    use ModularAPI\Abstracts\HTTP\ContentType;
    use ModularAPI\Abstracts\HTTP\FileType;
    use ModularAPI\Abstracts\HTTP\ResponseCode\ClientError;
    use ModularAPI\Abstracts\HTTP\ResponseCode\ServerError;
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
     * @throws AccessKeyNotFoundException
     * @throws ConfigurationNotFoundException
     * @throws DatabaseException
     * @throws InvalidSearchMethodException
     * @throws NoResultsFoundException
     * @throws PlanNotFoundException
     * @throws UnsupportedSearchMethodException
     * @throws UpdateRecordNotFoundException
     * @throws \IntellivoidAccounts\Exceptions\ConfigurationNotFoundException
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
            $Response = new Response();

            try
            {
                $IntellivoidAccounts->getTransactionRecordManager()->createTransaction(
                    $Plan->AccountId, $Plan->PricePerCycle, 'Intellivoid', TransactionType::SubscriptionPayment
                );
            }
            catch(InsufficientFundsException $insufficientFundsException)
            {
                $Plan->Active = false;
                $Plan->PaymentRequired = true;
                $OpenBlu->getPlanManager()->updatePlan($Plan);

                $Response->ResponseCode = ClientError::_400;
                $Response->ResponseType = ContentType::application . '/' . FileType::json;
                $Response->Content = array(
                    'status' => false,
                    'code' => ClientError::_400,
                    'message' => 'Insufficient funds for API Billing Cycle'
                );

                return $Response;
            }
            catch(Exception $exception)
            {
                $Response->ResponseCode = ServerError::_500;
                $Response->ResponseType = ContentType::application . '/' . FileType::json;
                $Response->Content = array(
                    'status' => false,
                    'code' => ServerError::_500,
                    'message' => 'There was an unknown issue while trying to process your API Subscription'
                );

                return $Response;
            }

            $Plan->NextBillingCycle = time() + $Plan->BillingCycle;
            $Plan->Active = true;
            $Plan->PaymentRequired = false;

            $OpenBlu->getPlanManager()->updatePlan($Plan);
        }

        return null;
    }