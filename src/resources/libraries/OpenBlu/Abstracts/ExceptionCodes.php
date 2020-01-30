<?php

    namespace OpenBlu\Abstracts;

    /**
     * Class ExceptionCodes
     * @package OpenBlu\Abstracts
     */
    abstract class ExceptionCodes
    {
        const ConfigurationNotFoundException = 100;

        const DatabaseException = 101;

        const InvalidSearchMethodException = 102;

        const UpdateRecordNotFoundException = 103;

        const SyncException = 104;

        const InvalidIPAddressException = 105;

        const VPNNotFoundException = 106;

        const PageNotFoundException = 107;

        /** @deprecated  */
        const PlanNotFoundException = 108;

        /** @deprecated  */
        const InvalidClientPropertyException = 109;

        /** @deprecated  */
        const ClientNotFoundException = 110;

        /** @deprecated  */
        const InvalidApiPlanTypeException = 111;

        /** @deprecated  */
        const LimitExceedingException = 112;

        const InvalidFilterTypeException = 113;

        const InvalidOrderByTypeException = 114;

        const InvalidOrderDirectionException = 115;

        const InvalidFilterValueException = 116;

        const NoResultsFoundException = 117;

        const UserSubscriptionRecordNotFoundException = 118;

    }