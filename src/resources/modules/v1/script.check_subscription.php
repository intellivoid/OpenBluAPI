<?php

    use COASniffle\Exceptions\CoaAuthenticationException;
    use COASniffle\Handlers\COA;
    use Handler\GenericResponses\InternalServerError;
    use IntellivoidAPI\Objects\AccessRecord;
    use IntellivoidSubscriptionManager\Abstracts\SearchMethods\SubscriptionSearchMethod;
    use IntellivoidSubscriptionManager\Exceptions\SubscriptionNotFoundException;
    use OpenBlu\Abstracts\SearchMethods\UserSubscriptionSearchMethod;
    use OpenBlu\Exceptions\UserSubscriptionRecordNotFoundException;
    use OpenBlu\OpenBlu;

    /**
     * This script gets executed by the main modules to determine if the subscription
     * is active or not. This has been written by Zi Xing for IVA2.0 & COA
     *
     * Version 1.0.0.0
     */

    /**
     * Processes the access key and determines if it used against a valid subscription.
     *
     * @param OpenBlu $openBlu
     * @param AccessRecord $accessRecord
     * @throws Exception
     */
    function validate_user_subscription(OpenBlu $openBlu, AccessRecord $accessRecord)
    {
        try
        {
            $UserSubscription = $openBlu->getUserSubscriptionManager()->getUserSubscription(
                UserSubscriptionSearchMethod::byAccessRecordID, $accessRecord->ID
            );
        }
        catch (UserSubscriptionRecordNotFoundException $e)
        {
            return script_cs_build_response(array(
                'success' => false,
                'response_code' => 403,
                'error_message' => 'Subscription not found'
                ), 403, array(
                    'access_record' => $accessRecord->toArray()
                )
            );
        }
        catch(Exception $e)
        {
            InternalServerError::executeResponse($e);
            exit();
        }

        /** @var IntellivoidSubscriptionManager\IntellivoidSubscriptionManager $IntellivoidSubscriptionManager */
        $IntellivoidSubscriptionManager = new IntellivoidSubscriptionManager\IntellivoidSubscriptionManager();

        try
        {
            $Subscription = $IntellivoidSubscriptionManager->getSubscriptionManager()->getSubscription(
                SubscriptionSearchMethod::byId, $UserSubscription->SubscriptionID
            );
        }
        catch (SubscriptionNotFoundException $e)
        {
            return script_cs_build_response(array(
                'success' => false,
                'response_code' => 403,
                'error_message' => 'You do not have an active subscription with this service'
                ), 403, array(
                    'access_record' => $accessRecord->toArray(),
                    'user_subscription' => $UserSubscription->toArray()
                )
            );
        }
        catch(Exception $e)
        {
            InternalServerError::executeResponse($e);
            exit();
        }

        if((int)time() > $Subscription->NextBillingCycle)
        {
            new COASniffle\COASniffle();

            try
            {
                COA::processSubscriptionBilling($Subscription->ID);
            }
            catch (CoaAuthenticationException $e)
            {
                return script_cs_build_response(array(
                    'success' => false,
                    'response_code' => 403,
                    'coa_error_code' => $e->getCode(),
                    'error_message' => $e->getMessage()
                ), 403, array(
                        'access_record' => $accessRecord->toArray(),
                        'user_subscription' => $UserSubscription->toArray(),
                        'subscription' => $Subscription->toArray()
                    )
                );
            }
            catch(Exception $e)
            {
                InternalServerError::executeResponse($e);
                exit();
            }
        }
    }

    /**
     * Builds a standard response which is understood by modules
     *
     * @param array $response_content
     * @param int $response_code
     * @param array $debugging_info
     * @return array
     */
    function script_cs_build_response(array $response_content, int $response_code, array $debugging_info): array
    {
        return array(
            'response' => $response_content,
            'response_code' => (int)$response_code,
            'debug' => $debugging_info
        );
    }