<?php

    namespace modules\v1;

    use Exception;
    use Handler\Abstracts\Module;
    use Handler\GenericResponses\InternalServerError;
    use Handler\Handler;
    use Handler\Interfaces\Response;
    use IntellivoidAPI\Objects\AccessRecord;
    use msqg\Abstracts\SortBy;
    use msqg\QueryBuilder;
    use OpenBlu\Exceptions\DatabaseException;
    use OpenBlu\OpenBlu;
    use SubscriptionValidation;

    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'script.check_subscription.php');

    /**
     * Class get_servers
     */
    class list_servers extends Module implements  Response
    {
        /**
         * The name of the module
         *
         * @var string
         */
        public $name = 'list_servers';

        /**
         * The version of this module
         *
         * @var string
         */
        public $version = '1.0.0.1';

        /**
         * The description of this module
         *
         * @var string
         */
        public $description = "Retrieves all the available VPN servers";

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
            return "";
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

            $where = null;
            $where_value = null;

            if(isset($Parameters['filter']))
            {
                if(isset($Parameters['by']))
                {
                    switch(strtolower($Parameters['filter']))
                    {
                        case 'country':
                            if(strlen($Parameters['by']) > 50)
                            {
                                $ResponsePayload = array(
                                    'success' => false,
                                    'response_code' => 400,
                                    'error' => array(
                                        'error_code' => 4,
                                        'type' => "CLIENT",
                                        "message" => "Invalid value for the parameter 'by'"
                                    )
                                );
                                $this->response_content = json_encode($ResponsePayload);
                                $this->response_code = (int)$ResponsePayload['response_code'];

                                return null;
                            }

                            $where = $OpenBlu->database->real_escape_string('country');
                            $where_value = $OpenBlu->database->real_escape_string($Parameters['by']);
                            break;

                        case 'country_short':
                            $where = $OpenBlu->database->real_escape_string('country_short');
                            $where_value = strtoupper(substr($Parameters['by'], 0, 2));
                            $where_value = $OpenBlu->database->real_escape_string($where_value);
                            break;

                        default:
                            $ResponsePayload = array(
                                'success' => false,
                                'response_code' => 400,
                                'error' => array(
                                    'error_code' => 5,
                                    'type' => "CLIENT",
                                    "message" => "Invalid value for the parameter 'filter'"
                                )
                            );
                            $this->response_content = json_encode($ResponsePayload);
                            $this->response_code = (int)$ResponsePayload['response_code'];

                            return null;
                    }
                }
                else
                {
                    $ResponsePayload = array(
                        'success' => false,
                        'response_code' => 400,
                        'error' => array(
                            'error_code' => 6,
                            'type' => "CLIENT",
                            "message" => "Missing parameter 'by'"
                        )
                    );
                    $this->response_content = json_encode($ResponsePayload);
                    $this->response_code = (int)$ResponsePayload['response_code'];

                    return null;
                }
            }

            $order_by = "last_updated";
            $sort_by = SortBy::descending;

            if(isset($Parameters['sort_by']))
            {
                switch($Parameters['sort_by'])
                {
                    case 'descending':
                        $sort_by = SortBy::descending;
                        break;

                    case 'ascending':
                        $sort_by = SortBy::ascending;
                        break;

                    default:
                        $ResponsePayload = array(
                            'success' => false,
                            'response_code' => 400,
                            'error' => array(
                                'error_code' => 7,
                                'type' => "CLIENT",
                                "message" => "Invalid value for the parameter 'sort_by'"
                            )
                        );
                        $this->response_content = json_encode($ResponsePayload);
                        $this->response_code = (int)$ResponsePayload['response_code'];

                        return null;
                }
            }

            if(isset($Parameters['order_by']))
            {
                switch(strtolower($Parameters['order_by']))
                {
                    case 'score':
                        $order_by = $OpenBlu->database->real_escape_string('score');
                        break;

                    case 'ping':
                        $order_by = $OpenBlu->database->real_escape_string('ping');
                        break;

                    case 'sessions':
                        $order_by = $OpenBlu->database->real_escape_string('sessions');
                        break;

                    case 'total_sessions':
                        $order_by = $OpenBlu->database->real_escape_string('total_sessions');
                        break;

                    case 'last_updated':
                        $order_by = $OpenBlu->database->real_escape_string('last_updated');
                        break;

                    case 'created':
                        $order_by = $OpenBlu->database->real_escape_string('created');
                        break;

                    default:
                        $ResponsePayload = array(
                            'success' => false,
                            'response_code' => 400,
                            'error' => array(
                                'error_code' => 8,
                                'type' => "CLIENT",
                                "message" => "Invalid value for the parameter 'order_by'"
                            )
                        );
                        $this->response_content = json_encode($ResponsePayload);
                        $this->response_code = (int)$ResponsePayload['response_code'];

                        return null;
                }
            }

            $Query = QueryBuilder::select('vpns', [
                'public_id',
                'country',
                'host_name',
                'score',
                'ping',
                'country',
                'country_short',
                'sessions',
                'total_sessions',
                'last_updated',
                'created'
            ], $where, $where_value, $order_by, $sort_by);
            $QueryResults = $OpenBlu->database->query($Query);

            if($QueryResults == false)
            {
                InternalServerError::executeResponse(new DatabaseException($OpenBlu->database->error, $Query));
                exit();
            }
            else
            {
                $ResultsArray = [];

                while($Row = $QueryResults->fetch_assoc())
                {
                    $ResultsArray[] = array(
                        'id' => $Row['public_id'],
                        'host_name' => $Row['host_name'],
                        'country' => $Row['country'],
                        'country_short' => $Row['country_short'],
                        'score' => (int)$Row['score'],
                        'ping' =>(int) $Row['ping'],
                        'sessions' => (int)$Row['sessions'],
                        'total_sessions' => (int)$Row['total_sessions'],
                        'last_updated' => (int)$Row['last_updated'],
                        'created' => (int)$Row['created']
                    );
                }
            }

            $ResponsePayload = array(
                'success' => true,
                'response_code' => 200,
                'servers' => $ResultsArray,
            );

            $OpenBlu->getDeepAnalytics()->tally('openblu_api', 'list_servers', 0);
            $OpenBlu->getDeepAnalytics()->tally('openblu_api', 'list_servers', $this->access_record->ID);
            $this->response_content = json_encode($ResponsePayload);
            $this->response_code = (int)$ResponsePayload['response_code'];
        }
    }