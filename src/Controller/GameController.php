<?php

namespace App\Controller;

use App\Service\TwitchApi;
use App\Service\ThemeInfo;
use App\Entity\HomeRowItem;
use App\Traits\ErrorLogTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;


class GameController extends AbstractController
{

    use ErrorLogTrait;

    /**
     * @Route("/game/{id}", name="game")
     */
    public function index(TwitchApi $twitch, $id): Response
    {

        if (!$this->isGranted('ROLE_LOCKED')) {
            return new RedirectResponse(
                $this->generateUrl('sonata_user_admin_security_login')
             );
        }

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
        try {
        if (!$this->isGranted('ROLE_LOCKED')) {
            return new RedirectResponse(
                $this->generateUrl('sonata_user_admin_security_login')
             );
        }

        $gameInfo = $gamersxCache->get("game-${id}",
            function (ItemInterface $item) use ($id, $twitch, $themeInfoService) {
                $game = $twitch->getGameInfo($id);
                $themeInfo = $themeInfoService->getThemeInfo($id, HomeRowItem::TYPE_GAME);
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

        } catch (ClientException $th) {
            $this->log_error($th->getMessage(). " " . $th->getFile() . " " . $th->getLine(), $th->getCode(), "game_api", null);
            throw $th;
        } catch (\Exception $ex) {
            $this->log_error($ex->getMessage(). " " . $ex->getFile() . " " . $ex->getLine(), $ex->getCode(), "game_api", null);
            throw $ex;
        }
    }

}
