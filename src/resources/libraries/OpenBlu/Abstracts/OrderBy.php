<?php


    namespace OpenBlu\Abstracts;

    /**
     * Class OrderBy
     * @package OpenBlu\Abstracts
     */
    abstract class OrderBy
    {
        const byLastUpdated = 'last_updated';

        const byPing = 'ping';

        const byScore = 'score';

        const byCurrentSessions = 'sessions';

        const byTotalSessions = 'total_sessions';
    }