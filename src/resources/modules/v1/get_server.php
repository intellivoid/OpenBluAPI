<?php

    namespace modules\v1;

    use Exception;
    use Handler\Abstracts\Module;
    use Handler\GenericResponses\InternalServerError;
    use Handler\Handler;
    use Handler\Interfaces\Response;
    use IntellivoidAPI\Objects\AccessRecord;
    use OpenBlu\Abstracts\SearchMethods\VPN;
    use OpenBlu\Exceptions\VPNNotFoundException;
    use OpenBlu\OpenBlu;
    use SubscriptionValidation;

    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'script.check_subscription.php');

    /**
     * Class get_servers
     */
    class get_server extends Module implements  Response
    {
        /**
         * The name of the module
         *
         * @var string
         */
        public $name = 'get_server';

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
        public $description = "Gets the server details and OpenVPN configuration data";

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
        private $response_code = 200;

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
            $SubscriptionValidation = new SubscriptionValidation();

            try
            {
                $ValidationResponse = $SubscriptionValidation->validateUserSubscription($OpenBlu, $this->access_record);
            }
            catch (Exception $e)
            {
                InternalServerError::executeResponse($e);
                exit();
            }

            if(is_null($ValidationResponse) == false)
            {
                $this->response_content = json_encode($ValidationResponse['response']);
                $this->response_code = $ValidationResponse['response_code'];

                return null;
            }

            $Parameters = Handler::getParameters(true, true);

            if(isset($Parameters['id']) == false)
            {
                $ResponsePayload = array(
                    'success' => false,
                    'response_code' => 400,
                    'error' => array(
                        'error_code' => 0,
                        'type' => "CLIENT",
                        "message" => "Missing parameter 'id'"
                    )
                );
                $this->response_content = json_encode($ResponsePayload);
                $this->response_code = (int)$ResponsePayload['response_code'];

                return null;
            }

            if(isset($this->access_record->Variables['SERVER_CONFIGS']) == false)
            {
                $this->access_record->setVariable('SERVER_CONFIGS', 0);
            }

            if($this->access_record->Variables['MAX_SERVER_CONFIGS'] > 0)
            {
                if($this->access_record->Variables['SERVER_CONFIGS'] -1 > $this->access_record->Variables['MAX_SERVER_CONFIGS'])
                {
                    $ResponsePayload = array(
                        'success' => false,
                        'response_code' => 429,
                        'error' => array(
                            'error_code' => 0,
                            'type' => "CLIENT",
                            "message" => "Server configuration quota limit reached"
                        )
                    );
                    $this->response_content = json_encode($ResponsePayload);
                    $this->response_code = (int)$ResponsePayload['response_code'];

                    return null;
                }
            }

            try
            {
                $Server = $OpenBlu->getVPNManager()->getVPN(VPN::byPublicID, $Parameters['id']);
                $this->access_record->Variables['SERVER_CONFIGS'] += 1;
            }
            catch (VPNNotFoundException $e)
            {
                $ResponsePayload = array(
                    'success' => false,
                    'response_code' => 404,
                    'error' => array(
                        'error_code' => 0,
                        'type' => "CLIENT",
                        "message" => "VPN Server not found"
                    )
                );
                $this->response_content = json_encode($ResponsePayload);
                $this->response_code = (int)$ResponsePayload['response_code'];

                return null;
            }
            catch(Exception $e)
            {
                InternalServerError::executeResponse($e);
                exit();
            }

            unset($Server->ConfigurationParameters['']);

            $ResponsePayload = array(
                'success' => true,
                'response_code' => 200,
                'server' => array(
                    'id' => $Server->PublicID,
                    'host_name' => $Server->HostName,
                    'country' => $Server->Country,
                    'country_short' => $Server->CountryShort,
                    'score' => (int)$Server->Score,
                    'ping' => (int)$Server->Ping,
                    'sessions' => $Server->Sessions,
                    'total_sessions' => $Server->TotalSessions,
                    'ip_address' => $Server->IP,
                    'openvpn' => array(
                        'parameters' => $Server->ConfigurationParameters,
                        'certificate_authority' => $Server->CertificateAuthority,
                        'certificate_authority_b64' => base64_encode($Server->CertificateAuthority),
                        'certificate' => $Server->Certificate,
                        'certificate_b64' => base64_encode($Server->Certificate),
                        'key' => $Server->Key,
                        'key_b64' => base64_encode($Server->Key),
                        'ovpn_configuration' => $Server->createConfiguration()
                    ),
                    'last_updated' => $Server->LastUpdated,
                    'created' => $Server->Created

                )
            );

            $this->response_content = json_encode($ResponsePayload);
            $this->response_code = (int)$ResponsePayload['response_code'];
        }
    }