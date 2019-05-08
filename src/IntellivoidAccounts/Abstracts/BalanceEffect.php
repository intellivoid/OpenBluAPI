<?php

    namespace IntellivoidAccounts\Abstracts;

    /**
     * Class BalanceEffect
     * @package IntellivoidAccounts\Abstracts
     */
    abstract class BalanceEffect
    {
        /**
         * Adds the given amount to the balance ($0.00)
         */
        const AddToBalance = 0;

        /**
         * Removes the given amount from the balance (-$0.00)
         */
        const DiscountFromBalance = 1;
    }