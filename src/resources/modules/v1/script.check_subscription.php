<?php

    use Handler\GenericResponses\InternalServerError;
    use IntellivoidAPI\Objects\AccessRecord;
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
     * @param AccessRecord $accessRecord
     */
    function validate_user_subscription(OpenBlu $openBlu, AccessRecord $accessRecord)
    {
        try
        {
            $openBlu->getUserSubscriptionManager()->getUserSubscription(
                UserSubscriptionSearchMethod::byAccessRecordID, $accessRecord->ID
            );
        }
        catch (UserSubscriptionRecordNotFoundException $e)
        {
            script_cs_build_response(array(
                'success' => false,
                'response_code' => 403,
                'error_message' => 'Subscription not found'
            ), 403, array(
                'access_record' => $accessRecord->toArray()
            ));
        }
        catch(Exception $e)
        {
            InternalServerError::executeResponse($e);
            exit();
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