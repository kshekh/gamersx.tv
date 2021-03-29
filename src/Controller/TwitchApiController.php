<?php

namespace App\Controller;

use App\Service\TwitchApi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api", name="twitch_")
 */
class TwitchApiController extends AbstractController
{
    /**
     * @Route("/query/streamer/{query}", name="queryStreamer")
     */
    public function channelQuery(Request $request, TwitchApi $twitch, $query)
    {
        $first = $request->get('first');
        $before = $request->get('before');
        $after = $request->get('after');

        $result = $twitch->searchChannels($query, $first, $before, $after);
        return $this->json($result->toArray());
    }

    /**
     * @Route("/query/game/{query}", name="queryGame")
     */
    public function gameQuery(Request $request, TwitchApi $twitch, $query)
    {
        $first = $request->get('first');
        $before = $request->get('before');
        $after = $request->get('after');

        $result = $twitch->searchGames($query, $first, $before, $after);
        return $this->json($result->toArray());
    }

    /**
     * @Route("/stream/popular", name="popularStreams")
     */
    public function getPopularStreams(Request $request, TwitchApi $twitch)
    {
        $first = $request->get('first');
        $before = $request->get('before');
        $after = $request->get('after');

        $result = $twitch->getPopularStreams($first, $before, $after);
        return $this->json($result->toArray());
    }

    /**
     * @Route("/streamer/{id}", name="streamer")
     */
    public function streamer(TwitchApi $twitch, $id)
    {
        $result = $twitch->getStreamerInfo($id);
        return $this->json($result->toArray());
    }

    /**
     * @Route("/game/{id}", name="game")
     */
    public function game(TwitchApi $twitch, $id)
    {
        $result = $twitch->getGameInfo($id);
        return $this->json($result->toArray());
    }

    /**
     * @Route("/stream/streamer/{id}", name="streamerStream")
     */
    public function getLiveBroadcast(TwitchApi $twitch, $id)
    {
        $result = $twitch->getStreamForStreamer($id);
        return $this->json($result->toArray());
    }

    /**
     * @Route("/stream/game/{id}", name="gamerStream")
     */
    public function getTopBroadcast(TwitchApi $twitch, $id)
    {
        $result = $twitch->getTopLiveBroadcastForGame($id);
        return $this->json($result->toArray());
    }

}
