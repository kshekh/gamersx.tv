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
        $queryParams = [
            'first' => $first,
            'query' => $query
        ];

        if ($before) {
            $queryParams['before'] = $before;
        } elseif ($after) {
            $queryParams['after'] = $after;
        }

        return $this->client->request('GET', '/helix/search/channels', [
            'query' => $queryParams
        ]);
    }

    public function searchGames($query, $first=20, $before=null, $after=null)
    {
        $queryParams = [
            'first' => $first,
            'query' => $query
        ];

        if ($before) {
            $queryParams['before'] = $before;
        } elseif ($after) {
            $queryParams['after'] = $after;
        }

        return $this->client->request('GET', '/helix/search/categories', [
            'query' => $queryParams
        ]);

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

    public function getChannelInfo($streamerId)
    {
        return $this->client->request('GET', '/helix/channels', [
            'query' => [
                'broadcaster_id' => $streamerId
            ]
        ]);
    }

    public function getTopLiveBroadcastsForGames($gameIds)
    {
        return $this->client->request('GET', '/helix/streams', [
            'query' => [
                'game_id' => $gameIds
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
        $query = ['first' => $first];

        if ($before) {
            $query['before'] = $before;
        } elseif ($after) {
            $query['after'] = $after;
        }

        return $this->client->request('GET', '/helix/streams', [
            'query' => $query
        ]);

    }

    public function getVideosForStreamer($streamerId, $first=8, $before=null, $after=null)
    {
        $query = [
            'user_id' => $streamerId,
            'first' => $first
        ];

        if ($before) {
            $query['before'] = $before;
        } elseif ($after) {
            $query['after'] = $after;
        }

        return $this->client->request('GET', '/helix/videos', [
            'query' => $query
        ]);
    }

}
