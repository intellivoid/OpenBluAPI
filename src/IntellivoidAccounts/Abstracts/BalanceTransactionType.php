<?php


    namespace IntellivoidAccounts\Abstracts;

    /**
     * Class BalanceTransactionType
     * @package IntellivoidAccounts\Abstracts
     */
    abstract class BalanceTransactionType
    {
        /**
         * Adds to the balance (either from a PayPal transaction or manually)
         */
        const AddToBalance = 0;

        /**
         * A purchase was made, subtracting from the balance
         */
        const Purchase = 1;

        /**
         * Adds to the balance from affiliation earnings
         */
        const AffiliationEarning = 2;

        /**
         * Subtracts from the balance to pay for a subscription
         */
        const SubscriptionPayment = 3;

        /**
         * Adds to the balance as a form of refund
         */
        const Refund = 4;

        /**
         * Subtracts from the balance to payout
         */
        const Payout = 5;
    }