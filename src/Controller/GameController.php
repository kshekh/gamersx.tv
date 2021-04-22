<?php

namespace App\Controller;

use App\Service\TwitchApi;
use App\Service\ThemeInfo;
use App\Entity\HomeRow;
use Doctrine\Common\Collections\ArrayCollection;
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
    public function apiGame(TwitchApi $twitch, ThemeInfo $themeInfoService,
        CacheInterface $gamersxCache, $id): Response
    {
        $gameInfo = $gamersxCache->get("game-${id}",
            function (ItemInterface $item) use ($id, $twitch, $themeInfoService) {
                $game = $twitch->getGameInfo($id);
                $themeInfo = $themeInfoService->getThemeInfo($id, HomeRow::ITEM_TYPE_GAME);
                $topFifty = $twitch->getTopLiveBroadcastForGame($id, 50)->toArray()['data'];

                $streams =  array_slice($topFifty, 0, 5);
                $totalStreamers = count($topFifty);
                $totalViewers = array_reduce($topFifty, function($sum, $broadcast) {
                    return $sum + $broadcast['viewer_count'];
                }, 0);

                if ($totalStreamers == 50) {
                    $totalStreamers = "50+";
                    $totalViewers = $totalViewers."+";
                }

                return [
                    'info' => $game->toArray()['data'][0],
                    'streams' => $streams,
                    'viewers' => $totalViewers,
                    'streamers' => $totalStreamers,
                    'theme' => $themeInfo
                ];
            });

        return $this->json($gameInfo);
    }

}
