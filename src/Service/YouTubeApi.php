<?php

namespace App\Service;

use Google_Client;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class YouTubeApi
{
    private $service;

    public function __construct(Google_Client $client)
    {
        $cache = new FilesystemAdapter();
        $client->setCache($cache);
        $this->service = new \Google_Service_YouTube($client);
    }

    public function getPopularStreams($first=20, $before=null, $after=null)
    {
        $queryParams = [
            'chart' => 'mostPopular',
            'regionCode' => 'US',
            'videoCategoryId' => '20'
        ];
        return $this->service->videos->listVideos('snippet', $queryParams);
    }


    public function getPopularChannelVideos($channelId, $first=8, $before=null, $after=null)
    {
        $queryParams = [
            'part' => 'snippet',
            'channelId' => $channelId,
            'order' => 'videoCount',
            'videoCategoryId' => '20',
            'type' => 'video',
        ];

        return $this->getPaginatedQuery($queryParams, $first, $before, $after);


    }
    public function searchChannels($query, $first=25, $before=null, $after=null)
    {
        $queryParams = [
            'q' => $query,
            'type' => 'channel'
        ];

        return $this->getPaginatedQuery($queryParams, $first, $before, $after);
    }

    public function getChannelInfo($channelIds)
    {
        if (is_array($channelIds)) {
            $channelIds = implode(',', $channelIds);
        }

        $queryParams = [
            'id' => $channelIds
        ];

        return $this->service->channels->listChannels('snippet', $queryParams);
    }

    public function getLiveChannel($channelId)
    {
        $queryParams = [
            'channelId' => $channelId,
            'eventType' => 'live',
            'order' => 'viewCount',
            'type' => 'video',
            'videoCategoryId' => '20',
            'maxResults' => 25,

        ];

        return $this->service->search->listSearch('snippet', $queryParams);
    }

    public function searchLiveChannels($query, $first=25, $before=null, $after=null)
    {
        $queryParams = [
            'q' => $query,
            'eventType' => 'live',
            'order' => 'viewCount',
            'type' => 'video',
            'videoCategoryId' => '20',
        ];
        return $this->getPaginatedQuery($queryParams, $first, $before, $after);
    }

    public function searchPopularVideos($query, $first=25, $before=null, $after=null)
    {
        $queryParams = [
            'q' => $query,
            'order' => 'viewCount',
            'type' => 'video',
            'videoCategoryId' => '20',
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
