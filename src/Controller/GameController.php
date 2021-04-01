<?php

namespace App\Controller;

use App\Service\TwitchApi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\CacheInterface;

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
    public function apiGame(TwitchApi $twitch, CacheInterface $gamersxCache, $id): Response
    {
        $gameInfo = $gamersxCache->get("game-${id}", function (ItemInterface $item) use ($id, $twitch) {
            $game = $twitch->getGameInfo($id);
            $streams = $twitch->getTopLiveBroadcastForGame($id, 5);

            return [
                'info' => $game->toArray()['data'][0],
                'streams' => $streams->toArray()['data']
            ];
        });

        return $this->json($gameInfo);
    }
}
