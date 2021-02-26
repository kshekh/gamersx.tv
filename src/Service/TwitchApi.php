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

    public function getQueryResults($query)
    {
        return $this->client->request('GET', '/helix/search/channels', [
            'query' => [
                'query' => $query
            ]
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
        return $this->client->request('GET', '/helix/channels', [
            'query' => [
                'broadcaster_id' => $streamerId
            ]
        ]);
    }

    public function getTopLiveBroadcastForGame($gameId)
    {
        return $this->client->request('GET', '/helix/streams', [
            'query' => [
                'game_id' => $gameId,
                'first' => 1
            ]
        ]);
    }
}
