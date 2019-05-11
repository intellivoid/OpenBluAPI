<?php

    /** @noinspection PhpUnusedParameterInspection */

    use ModularAPI\Abstracts\HTTP\ContentType;
    use ModularAPI\Abstracts\HTTP\FileType;
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
        $OpenBlu = new OpenBlu();
        $Results = $OpenBlu->getVPNManager()->filterGetServers(
            FilterType::None,
            'None',
            OrderBy::byCurrentSessions,
            OrderDirection::Ascending
        );

        $Response = new Response();
        $Response->ResponseCode = Successful::_200;
        $Response->ResponseType = ContentType::application . '/' . FileType::json;
        $Response->Content = array(
            'status' => true,
            'code' => Successful::_200,
            'payload' => $Results
        );

        return $Response;
    }