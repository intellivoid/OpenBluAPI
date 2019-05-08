<?php

    namespace IntellivoidAccounts\Abstracts;

    /**
     * Class TransactionStatus
     * @package IntellivoidAccounts\Abstracts
     */
    abstract class TransactionStatus
    {
        const None = 0;

        const ReviewInProgress = 1;

        const Approved = 2;

        const Denied = 3;
    }