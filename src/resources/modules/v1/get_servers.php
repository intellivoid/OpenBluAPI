<?php

    namespace modules\v1;

    use Handler\Abstracts\Module;
    use Handler\Interfaces\Response;
    use IntellivoidAPI\Objects\AccessRecord;
    use OpenBlu\OpenBlu;

    /**
     * Class get_servers
     */
    class get_servers extends Module implements  Response
    {
        /**
         * The name of the module
         *
         * @var string
         */
        public $name = 'get_servers';

        /**
         * The version of this module
         *
         * @var string
         */
        public $version = '1.0.0.0';

        /**
         * The description of this module
         *
         * @var string
         */
        public $description = "Retrieves a list of all the available servers that are available on OpenBlu";

        /**
         * Optional access record for this module
         *
         * @var AccessRecord
         */
        public $access_record;

        /**
         * The content to give on the response
         *
         * @var string
         */
        private $response_content;

        /**
         * The response code to be returned on the response
         *
         * @var int
         */
        private $response_code;

        /**
         * @inheritDoc
         */
        public function getContentType(): string
        {
            return 'application/json';
        }

        /**
         * @inheritDoc
         */
        public function getContentLength(): int
        {
            return strlen($this->response_content);
        }

        /**
         * @inheritDoc
         */
        public function getBodyContent(): string
        {
            return $this->response_content;
        }

        /**
         * @inheritDoc
         */
        public function getResponseCode(): int
        {
            return $this->response_code;
        }

        /**
         * @inheritDoc
         */
        public function isFile(): bool
        {
            return false;
        }

        /**
         * @inheritDoc
         */
        public function getFileName(): string
        {
            return null;
        }

        /**
         * @inheritDoc
         */
        public function processRequest()
        {
            $OpenBlu = new OpenBlu();

            // Import the check subscription script and execute it
            include_once(__DIR__ . DIRECTORY_SEPARATOR . 'script.check_subscription.php');
            $validation_response = validate_user_subscription($OpenBlu, $this->access_record);
            if(is_null($validation_response) == false)
            {
                $this->response_content = json_encode($validation_response['response']);
                $this->response_code = $validation_response['response_code'];

                return;
            }

            $ResponsePayload = array(
                'success' => true,
                'response_code' => 200,
                'payload' => $this->access_record->toArray(),
                'reference_code' => null
            );

            $this->response_content = json_encode($ResponsePayload);
            $this->response_code = (int)$ResponsePayload['response_code'];
        }
    }