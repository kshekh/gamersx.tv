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

    public function getTopLiveBroadcastForGame($gameId)
    {
        return $this->client->request('GET', '/helix/streams', [
            'query' => [
                'game_id' => $gameId,
                'first' => 1
            ]
        ]);
    }

    public function getStreamForStreamer($streamerId)
    {
        return $this->client->request('GET', '/helix/streams', [
            'query' => [
                'user_id' => $streamerId,
                // 'first' => 1
            ]
        ]);

    }

    public function getEmbedSettings($itemType, $id)
    {
        $settings = [
            'itemType' => $itemType,
            'embed' => FALSE
        ];

        if ($itemType === 'streamer') {
            $info = $this->getStreamerInfo($id);
            $settings['channel'] = $info['user_name'];
            $settings['img'] = $info['profile_image_url'];
        } elseif ($itemType === 'game') {
            $info = $this->getTopLiveBroadcastForGame($id);
            $settings['channel'] = $info['user_name'];
            $settings['img'] = $info['thumbnail_url'];
        }

        return $settings;
    }
}
