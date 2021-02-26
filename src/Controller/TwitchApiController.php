<?php

namespace App\Controller;

use App\Service\TwitchApi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api", name="twitch_")
 */
class TwitchApiController extends AbstractController
{
    /**
     * @Route("/query/{query}", name="query")
     */
    public function query(TwitchApi $twitch, $query)
    {
        $result = $twitch->getQueryResults($query);
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
     * @Route("/broadcast/{gameId}", name="broadcast")
     */
    public function getTopBroadcast(TwitchApi $twitch, $gameId)
    {
        $result = $twitch->getTopLiveBroadcastForGame($gameId);
        return $this->json($result->toArray());
    }
}
