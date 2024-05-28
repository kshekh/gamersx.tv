<?php

namespace App\Service;

use Google\Client;
use Google\Service\YouTube;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class YouTubeApi
{
    private $service;

    public function __construct(Client $client)
    {
        $cache = new FilesystemAdapter();
        $client->setCache($cache);
        $this->service = new \Google_Service_YouTube($client);
    }

    public function getPopularStreams($first=20, $before=null, $after=null): YouTube\VideoListResponse
    {
        $queryParams = [
            'chart' => 'mostPopular',
            'regionCode' => 'US',
            'videoCategoryId' => '20'
        ];
        return $this->service->videos->listVideos('snippet', $queryParams);
    }

    public function getLiveChannel($channelId): YouTube\SearchListResponse
    {
        $queryParams = [
            'part' => 'snippet',
            'channelId' => $channelId,
            'eventType' => 'live',
            'type' => 'video',
            'videoCategoryId' => '20',
        ];

        return $this->getPaginatedQuery($queryParams, 1, null, null);
    }

    public function getPopularChannelVideos($channelId, $first=8, $before=null, $after=null): YouTube\SearchListResponse
    {
        $queryParams = [
            'part' => 'snippet',
            'channelId' => $channelId,
            'order' => 'viewCount',
            'videoCategoryId' => '20',
            'type' => 'video',
        ];

        return $this->getPaginatedQuery($queryParams, $first, $before, $after);
    }

    public function getRecentChannelVideos($channelId, $first=8, $before=null, $after=null): YouTube\SearchListResponse
    {
        $queryParams = [
            'part' => 'snippet',
            'channelId' => $channelId,
            'order' => 'date',
            'videoCategoryId' => '20',
            'type' => 'video',
        ];

        return $this->getPaginatedQuery($queryParams, $first, $before, $after);
    }

    public function searchChannels($query, $first=25, $before=null, $after=null): YouTube\SearchListResponse
    {
        $queryParams = [
            'q' => $query,
            'type' => 'channel'
        ];

        return $this->getPaginatedQuery($queryParams, $first, $before, $after);
    }

    public function getChannelInfo($channelIds): YouTube\ChannelListResponse
    {
        if (is_array($channelIds)) {
            $channelIds = implode(',', $channelIds);
        }

        $queryParams = [
            'id' => $channelIds
        ];

        return $this->service->channels->listChannels('snippet,statistics', $queryParams);
    }

    public function searchLiveChannels($query, $first=25, $before=null, $after=null): YouTube\SearchListResponse
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

    public function getChannelVideosStats($videoIds): YouTube\VideoListResponse
    {
        if (is_array($videoIds)) {
            $videoIds = implode(',', $videoIds);
        }

        $part = 'statistics,liveStreamingDetails';

        $queryParams = [
            'id' => $videoIds,
        ];

        return $this->service->videos->listVideos($part, $queryParams);
    }

    public function searchPopularVideos($query, $first=25, $before=null, $after=null): YouTube\SearchListResponse
    {
        $queryParams = [
            'q' => $query,
            'order' => 'viewCount',
            'type' => 'video',
            'videoCategoryId' => '20',
        ];
        return $this->getPaginatedQuery($queryParams, $first, $before, $after);
    }

    public function getVideoInfo($videoIds): YouTube\VideoListResponse
    {
        if (is_array($videoIds)) {
            $videoIds = implode(',', $videoIds);
        }
        $queryParams = [
            'id' => $videoIds
        ];
        return $this->service->videos->listVideos('snippet,statistics,liveStreamingDetails', $queryParams);
    }

    public function getPlaylistInfo($playlistIds): YouTube\PlaylistListResponse
    {
        if (is_array($playlistIds)) {
            $playlistIds = implode(',', $playlistIds);
        }

        $queryParams = [
            'id' => $playlistIds
        ];

        return $this->service->playlists->listPlaylists('snippet', $queryParams);

    }

    public function getPlaylistItemsInfo($playlistIds): YouTube\PlaylistItemListResponse
    {
        if (is_array($playlistIds)) {
            $playlistIds = implode(',', $playlistIds);
        }

        $queryParams = [
            'playlistId' => $playlistIds
        ];

        return $this->service->playlistItems->listPlaylistItems('snippet', $queryParams);
    }

    /**
     * Helper method for API calls that use paginated queries
     */
    private function getPaginatedQuery($queryParams, $first, $before, $after): YouTube\SearchListResponse
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
