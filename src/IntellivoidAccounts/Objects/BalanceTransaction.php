<?php


    namespace IntellivoidAccounts\Objects;

    use IntellivoidAccounts\Abstracts\BalanceEffect;
    use IntellivoidAccounts\Abstracts\BalanceTransactionType;
    use IntellivoidAccounts\Abstracts\PaymentProcessor;

    /**
     * Class BalanceTransaction
     * @package IntellivoidAccounts\Objects
     */
    class BalanceTransaction
    {
        /**
         * The transaction ID
         *
         * @var int
         */
        public $ID;

        /**
         * The Public ID of the transaction
         *
         * @var string
         */
        public $PublicID;

        /**
         * The source of the transaction (eg; Supplier, PayPal, etc..)
         *
         * @var string
         */
        public $Source;

        /**
         * The type of transaction, if it's adding to the balance or making purchases
         *
         * @var BalanceTransactionType|int
         */
        public $Type;

        /**
         * Optional reference code to reference the transaction
         *
         * @var string
         */
        public $ReferenceCode;

        /**
         * @var PaymentProcessor|int
         */
        public $PaymentProcessor;

        /**
         * The transaction ID given by the Payment Processor
         *
         * @var string
         */
        public $ProcessorTransactionID;

        /**
         * The account ID associated with this transaction
         *
         * @var int
         */
        public $AccountID;

        /**
         * The amount that this transaction was placed for
         *
         * @var float
         */
        public $Amount;

        /**
         * @var BalanceEffect|int
         */
        public $BalanceEffect;

        /**
         * The old balance of the account before the transaction
         *
         * @var float
         */
        public $OldBalance;

        /**
         * The new balance of the account after the transaction
         *
         * @var float
         */
        public $NewBalance;

        /**
         * The Unix Timestamp of this transaction
         *
         * @var int
         */
        public $Timestamp;

        /**
         * Returns the object as an array
         *
         * @return array
         */
        public function toArray(): array
        {
            return array(
                'id' => (int)$this->ID,
                'public_id' => $this->PublicID,
                'source' => $this->Source,
                'type' => (int)$this->Type,
                'ref_code' => $this->ReferenceCode,
                'payment_processor' => (int)$this->PaymentProcessor,
                'processor_transaction_id' => $this->ProcessorTransactionID,
                'account_id' => (int)$this->AccountID,
                'amount' => (float)$this->Amount,
                'balance_effect' => (int)$this->BalanceEffect,
                'old_balance' => (float)$this->OldBalance,
                'new_balance' => (float)$this->NewBalance,
                'timestamp' => (int)$this->Timestamp
            );
        }

        /**
         * Creates object from array
         *
         * @param array $data
         * @return BalanceTransaction
         */
        public static function fromArray(array $data): BalanceTransaction
        {
            $BalanceTransaction = new BalanceTransaction();

            if(isset($data['id']))
            {
                $BalanceTransaction->ID = (int)$data['id'];
            }

            if(isset($data['public_id']))
            {
                $BalanceTransaction->PublicID = $data['public_id'];
            }

            if(isset($data['source']))
            {
                $BalanceTransaction->Source = $data['source'];
            }

            if(isset($data['type']))
            {
                $BalanceTransaction->Type = (int)$data['type'];
            }

            if(isset($data['ref_code']))
            {
                $BalanceTransaction->ReferenceCode = $data['ref_code'];
            }

            if(isset($data['payment_processor']))
            {
                $BalanceTransaction->PaymentProcessor = (int)$data['payment_processor'];
            }

            if(isset($data['processor_transaction_id']))
            {
                $BalanceTransaction->ProcessorTransactionID = $data['processor_transaction_id'];
            }

            if(isset($data['account_id']))
            {
                $BalanceTransaction->AccountID = (int)$data['account_id'];
            }

            if(isset($data['amount']))
            {
                $BalanceTransaction->Amount = (float)$data['amount'];
            }

            if(isset($data['balance_effect']))
            {
                $BalanceTransaction->BalanceEffect = (int)$data['balance_effect'];
            }

            if(isset($data['old_balance']))
            {
                $BalanceTransaction->OldBalance = (float)$data['old_balance'];
            }

            if(isset($data['new_balance']))
            {
                $BalanceTransaction->NewBalance = (float)$data['new_balance'];
            }

            if(isset($data['timestamp']))
            {
                $BalanceTransaction->Timestamp = (int)$data['timestamp'];
            }

            return $BalanceTransaction;
        }

    }