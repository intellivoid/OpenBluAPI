<?php

    namespace ModularAPI\Objects;

    /**
     * Class Paramerter
     * @package ModularAPI\Objects
     */
    class Paramerter
    {
        /**
         * The name of the Paramerter
         *
         * @var string
         */
        public $Name;

        /**
         * Indicates if this Parameter is required
         *
         * @var bool
         */
        public $Required;

        /**
         * The default value of this paramerter (If this paramerter is not required)
         *
         * @var string
         */
        public $Default;

        /**
         * Converts object to array
         * 
         * @return array
         */
        public function toArray(): array
        {
            return array(
                strtoupper($this->Name) => array(
                    'REQUIRED' => $this->Required,
                    'DEFAULT' => $this->Default
                )
            );
        }

        /**
         * Creates object from array
         * 
         * @param string $name
         * @param array $data
         * @return Paramerter
         */
        public static function fromArray(string $name, array $data): Paramerter
        {
            $ParamerterObject = new Paramerter();

            $ParamerterObject->Name = $name;

            if(isset($data['REQUIRED']))
            {
                $ParamerterObject->Required = (bool)$data['REQUIRED'];
            }
            else
            {
                $ParamerterObject->Required = true;
            }
            
            if(isset($data['DEFAULT']))
            {
                $ParamerterObject->Default = (string)$data['DEFAULT'];
            }
            else
            {
                $ParamerterObject->Default = null;
            }
            
            return $ParamerterObject;
        }
    }