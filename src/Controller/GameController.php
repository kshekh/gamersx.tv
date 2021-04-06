<?php

namespace App\Controller;

use App\Service\TwitchApi;
use App\Service\ThemeInfo;
use App\Entity\HomeRow;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

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
    public function apiGame(TwitchApi $twitch, ThemeInfo $themeInfo,
        CacheInterface $gamersxCache, $id): Response
    {
        $gameInfo = $gamersxCache->get("game-${id}",
            function (ItemInterface $item) use ($id, $twitch, $themeInfo) {
                $game = $twitch->getGameInfo($id);
                $streams = $twitch->getTopLiveBroadcastForGame($id, 5);
                $themeInfo = $themeInfo->getThemeInfo($id, HomeRow::ITEM_TYPE_GAME);

                return [
                    'info' => $game->toArray()['data'][0],
                    'streams' => $streams->toArray()['data'],
                    'theme' => $themeInfo
                ];
            });

        return $this->json($gameInfo);
    }

}
