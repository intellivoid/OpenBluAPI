<?php

    namespace OpenBlu\Objects;
    use OpenBlu\Abstracts\DefaultValues;

    /**
     * Class UpdateRecord
     * @package OpenBlu\Objects
     */
    class UpdateRecord
    {
        /**
         * The unique ID of the record
         *
         * @var int
         */
        public $ID;

        /**
         * The Public ID of the record
         *
         * @var string
         */
        public $PublicID;

        /**
         * The Unix Timestamp of when this record was made
         *
         * @var int
         */
        public $RequestTime;


        /**
         * Converts object to array
         *
         * @return array
         */
        public function toArray(): array
        {
            return array(
                'id' => $this->ID,
                'public_id' => $this->PublicID,
                'request_time' => $this->RequestTime
            );
        }

        /**
         * Creates object from array
         *
         * @param array $data
         * @return UpdateRecord
         */
        public static function fromArray(array $data): UpdateRecord
        {
            $UpdateRecordObject = new UpdateRecord();

            if(isset($data['id']))
            {
                $UpdateRecordObject->ID = (int)$data['id'];
            }
            else
            {
                $UpdateRecordObject->ID = 0;
            }

            if(isset($data['public_id']))
            {
                $UpdateRecordObject->PublicID = (string)$data['public_id'];
            }
            else
            {
                $UpdateRecordObject->PublicID = DefaultValues::Unknown;
            }

            if(isset($data['request_time']))
            {
                $UpdateRecordObject->RequestTime = (int)$data['request_time'];
            }
            else
            {
                $UpdateRecordObject->RequestTime = 0;
            }

            return $UpdateRecordObject;
        }
    }