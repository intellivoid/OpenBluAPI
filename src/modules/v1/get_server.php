<?php

    /** @noinspection PhpUnusedParameterInspection */

    use ModularAPI\Abstracts\HTTP\ContentType;
    use ModularAPI\Abstracts\HTTP\FileType;
    use ModularAPI\Abstracts\HTTP\ResponseCode\ClientError;
    use ModularAPI\Abstracts\HTTP\ResponseCode\ServerError;
    use ModularAPI\Abstracts\HTTP\ResponseCode\Successful;
    use ModularAPI\Objects\Response;
    use ModularAPI\Objects\AccessKey;
    use OpenBlu\Abstracts\SearchMethods\VPN;
    use OpenBlu\Exceptions\VPNNotFoundException;
    use OpenBlu\OpenBlu;

    function Module(?AccessKey $accessKey, array $Parameters): Response
    {
        $OpenBlu = new OpenBlu();

        try
        {
            $Server = $OpenBlu->getVPNManager()->getVPN(VPN::byPublicID, $Parameters['server_id']);
        }
        catch(VPNNotFoundException $VPNNotFoundException)
        {
            $Response = new Response();
            $Response->ResponseCode = ClientError::_404;
            $Response->ResponseType = ContentType::application . '/' . FileType::json;
            $Response->Content = array(
                'status' => false,
                'code' => ClientError::_404,
                'message' => 'The requested VPN server was not found'
            );

            return $Response;
        }
        catch(Exception $exception)
        {
            $Response = new Response();
            $Response->ResponseCode = ServerError::_500;
            $Response->ResponseType = ContentType::application . '/' . FileType::json;
            $Response->Content = array(
                'status' => false,
                'code' => ServerError::_500,
                'message' => 'Internal Server Error'
            );

            return $Response;
        }

        unset($Server->ConfigurationParameters['']);

        $Response = new Response();
        $Response->ResponseCode = Successful::_200;
        $Response->ResponseType = ContentType::application . '/' . FileType::json;
        $Response->Content = array(
            'status' => true,
            'code' => Successful::_200,
            'payload' => array(
                'id' => $Server->PublicID,
                'host_name' => $Server->HostName,
                'ip_address' => $Server->IP,
                'score' => (int)$Server->Score,
                'ping' => (int)$Server->Ping,
                'country' => $Server->Country,
                'country_short' => $Server->CountryShort,
                'sessions' => $Server->Sessions,
                'total_sessions' => $Server->TotalSessions,
                'openvpn' => array(
                    'parameters' => $Server->ConfigurationParameters,
                    'certificate_authority' => $Server->CertificateAuthority,
                    'certificate' => $Server->Certificate,
                    'key' => $Server->Key,
                    'ovpn_configuration' => $Server->createConfiguration()
                ),
                'last_updated' => $Server->LastUpdated,
                'created' => $Server->Created

            )
        );

        return $Response;

    }