<?php

    /** @noinspection PhpUnusedParameterInspection */

    use ModularAPI\Abstracts\HTTP\ContentType;
    use ModularAPI\Abstracts\HTTP\FileType;
    use ModularAPI\Abstracts\HTTP\ResponseCode\ClientError;
    use ModularAPI\Abstracts\HTTP\ResponseCode\Successful;
    use ModularAPI\Objects\AccessKey;
    use ModularAPI\Objects\Response;
    use OpenBlu\Abstracts\FilterType;
    use OpenBlu\Abstracts\OrderBy;
    use OpenBlu\Abstracts\OrderDirection;
    use OpenBlu\Exceptions\ConfigurationNotFoundException;
    use OpenBlu\Exceptions\DatabaseException;
    use OpenBlu\Exceptions\InvalidFilterTypeException;
    use OpenBlu\Exceptions\InvalidFilterValueException;
    use OpenBlu\Exceptions\InvalidOrderByTypeException;
    use OpenBlu\Exceptions\InvalidOrderDirectionException;
    use OpenBlu\Exceptions\NoResultsFoundException;
    use OpenBlu\OpenBlu;

    /**
     * @param AccessKey $accessKey
     * @param array $Parameters
     * @return Response
     * @throws ConfigurationNotFoundException
     * @throws DatabaseException
     * @throws InvalidFilterTypeException
     * @throws InvalidFilterValueException
     * @throws InvalidOrderByTypeException
     * @throws InvalidOrderDirectionException
     * @throws NoResultsFoundException
     */
    function Module(?AccessKey $accessKey, array $Parameters): Response
    {
        $FilterBy = FilterType::None;
        $FilterValue = 'None';
        $OrderBy = OrderBy::byTotalSessions;
        $OrderDirection = OrderDirection::Ascending;

        switch(strtolower($Parameters['filter_by']))
        {
            case 'none':
                $FilterBy = FilterType::None;
                $FilterValue = 'None';
                break;

            case 'country':
                $FilterBy = FilterType::byCountry;
                if(strlen($Parameters['filter']) !== 2)
                {
                    $Response = new Response();
                    $Response->ResponseCode = ClientError::_400;
                    $Response->ResponseType = ContentType::application . '/' . FileType::json;
                    $Response->Content = array(
                        'status' => false,
                        'status_code' => ClientError::_400,
                        'message' =>'The given filter value is invalid'
                    );

                    return $Response;
                }

                $FilterValue = strtoupper($Parameters['filter']);

                break;

            default:
                $Response = new Response();
                $Response->ResponseCode = ClientError::_400;
                $Response->ResponseType = ContentType::application . '/' . FileType::json;
                $Response->Content = array(
                    'status' => false,
                    'status_code' => ClientError::_400,
                    'message' =>'The filter type is invalid'
                );

                return $Response;
        }

        switch(strtolower($Parameters['order_by']))
        {
            case 'last_updated':
                $OrderBy = OrderBy::byLastUpdated;
                break;

            case 'ping':
                $OrderBy = OrderBy::byPing;
                break;

            case 'score':
                $OrderBy = OrderBy::byScore;
                break;

            case 'sessions':
                $OrderBy = OrderBy::byCurrentSessions;
                break;

            case 'total_sessions':
                $OrderBy = OrderBy::byTotalSessions;
                break;

            default:
                $Response = new Response();
                $Response->ResponseCode = ClientError::_400;
                $Response->ResponseType = ContentType::application . '/' . FileType::json;
                $Response->Content = array(
                    'status' => false,
                    'status_code' => ClientError::_400,
                    'message' => 'The order type is invalid'
                );

                return $Response;
        }

        switch(strtolower($Parameters['order_direction']))
        {
            case 'ascending':
                $OrderDirection = OrderDirection::Ascending;
                break;

            case 'descending':
                $OrderDirection = OrderDirection::Descending;
                break;

            default:
                $Response = new Response();
                $Response->ResponseCode = ClientError::_400;
                $Response->ResponseType = ContentType::application . '/' . FileType::json;
                $Response->Content = array(
                    'status' => false,
                    'status_code' => ClientError::_400,
                    'message' => 'The order direction can only be ascending or descending'
                );

                return $Response;
        }

        $OpenBlu = new OpenBlu();
        $Results = $OpenBlu->getVPNManager()->filterGetServers($FilterBy, $FilterValue, $OrderBy, $OrderDirection);
        $PayloadResults = array();

        foreach($Results as $Server)
        {
            $PayloadResults[] = array(
                'public_id' => $Server['public_id'],
                'score' => (int)$Server['score'],
                'ping' => (int)$Server['ping'],
                'country' => $Server['country'],
                'country_short' => $Server['country_short'],
                'sessions' => (int)$Server['sessions'],
                'total_sessions' => (int)$Server['total_sessions'],
                'last_updated' => (int)$Server['last_updated'],
                'created' => (int)$Server['created']
            );
        }

        $Response = new Response();
        $Response->ResponseCode = Successful::_200;
        $Response->ResponseType = ContentType::application . '/' . FileType::json;
        $Response->Content = array(
            'status' => true,
            'status_code' => Successful::_200,
            'payload' => $PayloadResults
        );

        return $Response;
    }