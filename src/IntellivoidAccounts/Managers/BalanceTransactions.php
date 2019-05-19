<?php


    namespace IntellivoidAccounts\Managers;

    use IntellivoidAccounts\Abstracts\AccountStatus;
    use IntellivoidAccounts\Abstracts\BalanceEffect;
    use IntellivoidAccounts\Abstracts\BalanceTransactionType;
    use IntellivoidAccounts\Abstracts\PaymentProcessor;
    use IntellivoidAccounts\Abstracts\SearchMethods\AccountSearchMethod;
    use IntellivoidAccounts\Abstracts\SearchMethods\BalanceTransactionSearchMethod;
    use IntellivoidAccounts\Exceptions\AccountLimitedException;
    use IntellivoidAccounts\Exceptions\AccountNotFoundException;
    use IntellivoidAccounts\Exceptions\AccountSuspendedException;
    use IntellivoidAccounts\Exceptions\BalanceTransactionRecordNotFoundException;
    use IntellivoidAccounts\Exceptions\DatabaseException;
    use IntellivoidAccounts\Exceptions\InvalidAccountStatusException;
    use IntellivoidAccounts\Exceptions\InvalidEmailException;
    use IntellivoidAccounts\Exceptions\InvalidPasswordException;
    use IntellivoidAccounts\Exceptions\InvalidSearchMethodException;
    use IntellivoidAccounts\Exceptions\InvalidUsernameException;
    use IntellivoidAccounts\IntellivoidAccounts;
    use IntellivoidAccounts\Objects\BalanceTransaction;
    use IntellivoidAccounts\Utilities\Hashing;

    /**
     * Class BalanceTransactions
     * @package IntellivoidAccounts\Managers
     */
    class BalanceTransactions
    {
        /**
         * @var IntellivoidAccounts
         */
        private $intellivoidAccounts;

        /**
         * BalanceTransactions constructor.
         * @param IntellivoidAccounts $intellivoidAccounts
         */
        public function  __construct(IntellivoidAccounts $intellivoidAccounts)
        {
            $this->intellivoidAccounts =  $intellivoidAccounts;
        }

        /**
         * Registers a balance transaction record
         *
         * Properties that will be overwritten by this function, if it's already been set
         * then it will simply be ignored since these properties are required to be
         * unique, and this function will determine the best values
         *
         * @readonly BalanceTransaction->ID
         * @readonly BalanceTransaction->PublicID
         * @readonly BalanceTransaction->Timestamp
         *
         * @param BalanceTransaction $balanceTransaction
         * @return BalanceTransaction
         * @throws BalanceTransactionRecordNotFoundException
         * @throws DatabaseException
         * @throws InvalidSearchMethodException
         */
        public function registerTransaction(BalanceTransaction $balanceTransaction): BalanceTransaction
        {
            $Timestamp = (int)time();
            $PublicID = $this->intellivoidAccounts->database->real_escape_string(Hashing::balanceTransactionPublicID(
                $balanceTransaction->AccountID,
                $Timestamp,
                $balanceTransaction->Amount,
                $balanceTransaction->Source
            ));
            $Source = $this->intellivoidAccounts->database->real_escape_string($balanceTransaction->Source);
            $Type = (int)$balanceTransaction->Type;
            $ReferenceCode = $this->intellivoidAccounts->database->real_escape_string($balanceTransaction->ReferenceCode);
            $PaymentProcessor = (int)$balanceTransaction->PaymentProcessor;
            $ProcessorTransactionID = $this->intellivoidAccounts->database->real_escape_string($balanceTransaction->ProcessorTransactionID);
            $AccountID = (int)$balanceTransaction->AccountID;
            $Amount = (float)$balanceTransaction->Amount;
            $BalanceEffect = (int)$balanceTransaction->BalanceEffect;
            $OldBalance = (float)$balanceTransaction->OldBalance;
            $NewBalance = $balanceTransaction->NewBalance;

            $Query = sprintf("INSERT INTO `balance_transactions` (public_id, source, type, ref_code, payment_processor, processor_transaction_id, account_id, amount, balance_effect, old_balance, new_balance, timestamp) VALUES ('%s', '%s', %s, '%s', %s, '%s', %s, %s, %s, %s, %s, %s)", $PublicID, $Source, $Type, $ReferenceCode, $PaymentProcessor, $ProcessorTransactionID, $AccountID, $Amount, $BalanceEffect, $OldBalance, $NewBalance, $Timestamp);
            $QueryResults = $this->intellivoidAccounts->database->query($Query);

            if($QueryResults == true)
            {
                return $this->getRecord(BalanceTransactionSearchMethod::byPublicId, $PublicID);
            }
            else
            {
                throw new DatabaseException($Query, $this->intellivoidAccounts->database->error);
            }
        }

        /**
         * Returns an existing record from the database
         *
         * @param string $search_method
         * @param string $value
         * @return BalanceTransaction
         * @throws BalanceTransactionRecordNotFoundException
         * @throws DatabaseException
         * @throws InvalidSearchMethodException
         */
        public function getRecord(string $search_method, string $value): BalanceTransaction
        {
            switch($search_method)
            {
                case BalanceTransactionSearchMethod::byId:
                    $search_method = $this->intellivoidAccounts->database->real_escape_string($search_method);
                    $value = (int)$value;
                    break;

                case BalanceTransactionSearchMethod::byPublicId:
                    $search_method = $this->intellivoidAccounts->database->real_escape_string($search_method);
                    $value = "'$value'";
                    break;

                default:
                    throw new InvalidSearchMethodException();
            }

            $Query = "SELECT id, public_id, source, type, ref_code, payment_processor, processor_transaction_id, account_id, amount, balance_effect, old_balance, new_balance, timestamp FROM `balance_transactions` WHERE $search_method=$value";
            $QueryResults = $this->intellivoidAccounts->database->query($Query);

            if($QueryResults == false)
            {
                throw new DatabaseException($Query, $this->intellivoidAccounts->database->error);
            }
            else
            {
                if($QueryResults->num_rows !== 1)
                {
                    throw new BalanceTransactionRecordNotFoundException();
                }

                return BalanceTransaction::fromArray($QueryResults->fetch_array(MYSQLI_ASSOC));
            }
        }

        /**
         * Simply adds to the account balance without any payment processors
         *
         * @param int $account_id
         * @param float $amount
         * @param string $source
         * @param BalanceTransactionType|int $type
         * @param string $reference
         * @return BalanceTransaction
         * @throws AccountLimitedException
         * @throws AccountNotFoundException
         * @throws AccountSuspendedException
         * @throws DatabaseException
         * @throws InvalidSearchMethodException
         * @throws InvalidAccountStatusException
         * @throws InvalidEmailException
         * @throws InvalidPasswordException
         * @throws InvalidUsernameException
         * @throws BalanceTransactionRecordNotFoundException
         */
        public function addBalanceToAccount(int $account_id, float $amount, string $source, int $type, string $reference = 'None'): BalanceTransaction
        {
            $AccountObject = $this->intellivoidAccounts->getAccountManager()->getAccount(AccountSearchMethod::byId, $account_id);

            if($AccountObject->Status == AccountStatus::Suspended)
            {
                throw new AccountSuspendedException();
            }

            if($AccountObject->Status == AccountStatus::Limited)
            {
                throw new AccountLimitedException();
            }

            $Transaction = new BalanceTransaction();
            $Transaction->Source = $source;
            $Transaction->Type = $type;
            $Transaction->ReferenceCode = $reference;
            $Transaction->PaymentProcessor = PaymentProcessor::None;
            $Transaction->ProcessorTransactionID = 'None';
            $Transaction->AccountID = $AccountObject->ID;
            $Transaction->Amount = $amount;
            $Transaction->BalanceEffect = BalanceEffect::AddToBalance;
            $Transaction->OldBalance = $AccountObject->Configuration->Balance;
            $Transaction->NewBalance = $AccountObject->Configuration->Balance + $amount;

            // Update the account first
            $AccountObject->Configuration->Balance = $AccountObject->Configuration->Balance + $amount;
            $this->intellivoidAccounts->getAccountManager()->updateAccount($AccountObject);

            // Log the transaction
            return $this->registerTransaction($Transaction);
        }
    }