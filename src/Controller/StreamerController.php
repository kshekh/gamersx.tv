<?php

namespace App\Controller;

use App\Service\TwitchApi;
use App\Service\ThemeInfo;
use App\Entity\HomeRowItem;
use App\Traits\ErrorLogTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\CacheInterface;


use Symfony\Component\HttpFoundation\RedirectResponse;

class StreamerController extends AbstractController
{
    use ErrorLogTrait;

    /**
     * @Route("/streamer/{id}", name="streamer")
     */
    public function index(TwitchApi $twitch, $id): Response
    {

        if (!$this->isGranted('ROLE_LOCKED')) {
            return new RedirectResponse(
                $this->generateUrl('sonata_user_admin_security_login')
             );
        }

        return $this->render('streamer/index.html.twig', [
            'dataUrl' => $this->generateUrl('streamer_api', [
                'id' => $id
            ])
        ]);
    }

    /**
     * @Route("/streamer/{id}/api", name="streamer_api")
     */
    public function apiStreamer(TwitchApi $twitch, ThemeInfo $themeInfoService,
        CacheInterface $gamersxCache, $id): Response
    {
        try{

        if (!$this->isGranted('ROLE_LOCKED')) {
            return new RedirectResponse(
                $this->generateUrl('sonata_user_admin_security_login')
             );
        }

        $streamerInfo = $gamersxCache->get("streamer-${id}",
            function (ItemInterface $item) use ($id, $twitch, $themeInfoService) {

            $streamer = $twitch->getStreamerInfo($id);
            $followers = $twitch->getFollowersForStreamer($id);
            $broadcast = $twitch->getStreamForStreamer($id);
            $videos = $twitch->getVideosForStreamer($id);
            $themeInfo = $themeInfoService->getThemeInfo($id, HomeRowItem::TYPE_STREAMER);

            return [
                'info' => $streamer->toArray()['data'][0],
                'followers' => $followers->toArray()['total'],
                'broadcast' => $broadcast->toArray()['data'],
                'vods' => $videos->toArray()['data'],
                'theme' => $themeInfo
            ];
        });

        return $this->json($streamerInfo);
        } catch (ClientException $th) {
            $this->log_error($th->getMessage(). " " . $th->getFile() . " " . $th->getLine(), $th->getCode(), "streamer_api", null);
            throw $th;
        } catch (\Exception $ex) {
            $this->log_error($ex->getMessage(). " " . $ex->getFile() . " " . $ex->getLine(), $ex->getCode(), "streamer_api", null);
            throw $ex;
        }
    }

    /**
     * @Route("/streamer/info/{channel}/api", name="streamer_infor_api")
     */
    public function apiStreamerInfo($channel, TwitchApi $twitch): Response
    {
        return $this->json($twitch->getStreamerInfoByChannel($channel));
    }
}
