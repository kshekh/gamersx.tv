<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class TwitchApi
{
    private $client;

    public function __construct(HttpClientInterface $twitch)
    {
        $this->client = $twitch;
    }

    public function searchChannels($query, $first=20, $before=null, $after=null)
    {
        return $this->getPaginatedQuery('/helix/search/channels', [
            'query' => $query
        ], $first, $before, $after);
    }

    public function searchGames($query, $first=20, $before=null, $after=null)
    {
        return $this->getPaginatedQuery('/helix/search/categories', [
            'query' => $query
        ], $first, $before, $after);
    }

    public function getGameInfo($gameId)
    {
        return $this->client->request('GET', '/helix/games', [
            'query' => [
                'id' => $gameId
            ]
        ]);
    }

    public function getStreamerInfo($streamerId)
    {
        return $this->client->request('GET', '/helix/users', [
            'query' => [
                'id' => $streamerId
            ]
        ]);
    }

    public function getTopLiveBroadcastForGame($gameId)
    {
        return $this->client->request('GET', '/helix/streams', [
            'query' => [
                'game_id' => $gameId
            ]
        ]);
    }

    public function getStreamForStreamer($streamerId)
    {
        return $this->client->request('GET', '/helix/streams', [
            'query' => [
                'user_id' => $streamerId,
            ]
        ]);

    }

    public function getPopularStreams($first=20, $before=null, $after=null)
    {
        return $this->getPaginatedQuery('/helix/streams', [],
            $first, $before, $after);
    }

    public function getVideosForStreamer($streamerId, $first=8, $before=null, $after=null)
    {
        return $this->getPaginatedQuery('/helix/videos', [
            'user_id' => $streamerId,
        ], $first, $before, $after);
    }

    /**
     * Helper method for API calls that use paginated queries
     */
    private function getPaginatedQuery($url, $queryParams, $first, $before, $after)
    {
        $query = ['first' => $first];

        if ($before) {
            $queryParams['before'] = $before;
        } elseif ($after) {
            $queryParams['after'] = $after;
        }

        return $this->client->request('GET', $url, [
            'query' => $queryParams
        ]);
    }
}
