<?php


    namespace OpenBlu\Abstracts\SearchMethods;

    /**
     * Class UserSubscriptionSearchMethod
     * @package OpenBlu\Abstracts\SearchMethods
     */
    abstract class UserSubscriptionSearchMethod
    {
        const byId = 'id';

        const byAccountID = 'account_id';

        const bySubscriptionID = 'subscription_id';

        const byAccessRecordID = 'access_record_id';
    }