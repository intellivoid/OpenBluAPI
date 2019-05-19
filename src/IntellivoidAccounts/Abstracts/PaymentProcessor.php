<?php


    namespace IntellivoidAccounts\Abstracts;

    /**
     * Class PaymentProcessor
     * @package IntellivoidAccounts\Abstracts
     */
    abstract class PaymentProcessor
    {
        /**
         * No payment processor was used
         */
        const None = 0;

        /**
         * Another unlisted Payment Processor was used
         */
        const Other = 1;

        /**
         * PayPal Payment processor was used
         */
        const PayPal = 2;
    }