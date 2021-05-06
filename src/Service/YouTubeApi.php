<?php

namespace App\Service;

use Google_Client;

class YouTubeApi
{
    private $service;

    public function __construct(Google_Client $client)
    {
        $this->service = new \Google_Service_YouTube($client);
    }

    public function getPopularStreams($first=20, $before=null, $after=null)
    {
        return $this->getPaginatedQuery('/helix/streams', [],
            $first, $before, $after);
    }

    public function searchChannels($query, $first=25, $before=null, $after=null)
    {
        $queryParams = [
            'q' => $query,
            'type' => 'channel'
        ];

        return $this->getPaginatedQuery($queryParams, $first, $before, $after);
    }

    /**
     * Helper method for API calls that use paginated queries
     */
    private function getPaginatedQuery($queryParams, $first, $before, $after)
    {
        $queryParams['maxResults'] = $first;

        if ($before) {
            $queryParams['prevPageToken'] = $before;
        } elseif ($after) {
            $queryParams['nextPageToken'] = $after;
        }

        return $this->service->search->listSearch('snippet', $queryParams);
    }

}
