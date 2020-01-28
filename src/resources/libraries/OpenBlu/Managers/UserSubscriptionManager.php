<?php


    namespace OpenBlu\Managers;


    use msqg\QueryBuilder;
    use OpenBlu\Abstracts\SearchMethods\UserSubscriptionSearchMethod;
    use OpenBlu\Abstracts\UserSubscriptionStatus;
    use OpenBlu\Exceptions\DatabaseException;
    use OpenBlu\Exceptions\InvalidSearchMethodException;
    use OpenBlu\Exceptions\UserSubscriptionRecordNotFoundException;
    use OpenBlu\Objects\UserSubscription;
    use OpenBlu\OpenBlu;

    /**
     * Class UserSubscriptionManager
     * @package OpenBlu\Managers
     */
    class UserSubscriptionManager
    {
        /**
         * @var OpenBlu
         */
        private $openBlu;

        /**
         * UserSubscriptionManager constructor.
         * @param OpenBlu $openBlu
         */
        public function __construct(OpenBlu $openBlu)
        {
            $this->openBlu = $openBlu;
        }

        /**
         * Registers a User Subscription into the database
         *
         * @param int $account_id
         * @param int $subscription_id
         * @param int $access_record_id
         * @return UserSubscription
         * @throws DatabaseException
         * @throws InvalidSearchMethodException
         * @throws UserSubscriptionRecordNotFoundException
         */
        public function registerUserSubscription(int $account_id, int $subscription_id, int $access_record_id): UserSubscription
        {
            $account_id = (int)$account_id;
            $subscription_id = (int)$subscription_id;
            $access_record_id = (int)$access_record_id;
            $status = (int)UserSubscriptionStatus::Active;
            $created_timestamp = (int)time();

            $Query = QueryBuilder::insert_into('user_subscriptions', array(
                'account_id' => $account_id,
                'subscription_id' => $subscription_id,
                'access_record_id' => $access_record_id,
                'status' => $status,
                'created_timestamp' => $created_timestamp
            ));
            $QueryResults = $this->openBlu->database->query($Query);

            if($QueryResults == true)
            {
                return $this->getUserSubscription(UserSubscriptionSearchMethod::byAccountID, $account_id);
            }
            else
            {
                throw new DatabaseException($this->openBlu->database->error, $Query);
            }
        }

        /**
         * Returns a user subscription record from the database
         *
         * @param string $search_method
         * @param int $value
         * @return UserSubscription
         * @throws DatabaseException
         * @throws InvalidSearchMethodException
         * @throws UserSubscriptionRecordNotFoundException
         */
        public function getUserSubscription(string $search_method, int $value): UserSubscription
        {
            switch($search_method)
            {
                case UserSubscriptionSearchMethod::byId:
                case UserSubscriptionSearchMethod::bySubscriptionID:
                case UserSubscriptionSearchMethod::byAccessRecordID:
                case UserSubscriptionSearchMethod::byAccountID:
                    $search_method = $this->openBlu->database->real_escape_string($search_method);
                    $value = (int)$value;
                    break;

                default:
                    throw new InvalidSearchMethodException();
            }

            $Query = QueryBuilder::select('user_subscriptions', [
                'id', 'account_id', 'subscription_id', 'access_record_id', 'status', 'created_timestamp'
            ], $search_method, $value);
            $QueryResults = $this->openBlu->database->query($Query);

            if($QueryResults == false)
            {
                throw new DatabaseException($this->openBlu->database->error, $Query);
            }
            else
            {
                if($QueryResults->num_rows !== 1)
                {
                    throw new UserSubscriptionRecordNotFoundException();
                }

                $Row = $QueryResults->fetch_array(MYSQLI_ASSOC);

                return UserSubscription::fromArray($Row);
            }
        }

        /**
         * Updates an existing user subscription record in the database
         *
         * @param UserSubscription $userSubscription
         * @return bool
         * @throws DatabaseException
         * @throws InvalidSearchMethodException
         * @throws UserSubscriptionRecordNotFoundException
         */
        public function updateUserSubscription(UserSubscription $userSubscription): bool
        {
            $this->getUserSubscription(UserSubscriptionSearchMethod::byId, $userSubscription->ID);

            $id = (int)$userSubscription->ID;
            $account_id = (int)$userSubscription->AccountID;
            $subscription_id = (int)$userSubscription->SubscriptionID;
            $access_record_id = (int)$userSubscription->AccessRecordID;
            $status = (int)$userSubscription->Status;

            $Query = QueryBuilder::update('user_subscriptions', array(
                'account_id' => $account_id,
                'subscription_id' => $subscription_id,
                'access_record_id' => $access_record_id,
                'status' => $status
            ), 'id', $id);
            $QueryResults = $this->openBlu->database->query($Query);

            if($QueryResults == true)
            {
                return true;
            }
            else
            {
                throw new DatabaseException($this->openBlu->database->error, $Query);
            }
        }
    }