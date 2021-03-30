<?php

namespace App\Controller;

use App\Service\TwitchApi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    /**
     * @Route("/game/{id}", name="game")
     */
    public function index(TwitchApi $twitch, $id): Response
    {
        return $this->render('game/index.html.twig', [
            'dataUrl' => $this->generateUrl('game_api', [
                'id' => $id
            ])
        ]);
    }

    /**
     * @Route("/game/{id}/api", name="game_api")
     */
    public function apiStreamer(TwitchApi $twitch, $id): Response
    {
        $game = $twitch->getGameInfo($id);
        $streams = $twitch->getTopLiveBroadcastForGame($id, 5);

        return $this->json([
            'info' => $game->toArray()['data'][0],
            'streams' => $streams->toArray()['data']
        ]);
    }
}
