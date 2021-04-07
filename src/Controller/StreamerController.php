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

class StreamerController extends AbstractController
{
    /**
     * @Route("/streamer/{id}", name="streamer")
     */
    public function index(TwitchApi $twitch, $id): Response
    {
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
        $streamerInfo = $gamersxCache->get("streamer-${id}",
            function (ItemInterface $item) use ($id, $twitch, $themeInfoService) {

            $streamer = $twitch->getStreamerInfo($id);
            $videos = $twitch->getVideosForStreamer($id);
            $themeInfo = $themeInfoService->getThemeInfo($id, HomeRow::ITEM_TYPE_STREAMER);

            return [
                'info' => $streamer->toArray()['data'][0],
                'vods' => $videos->toArray()['data'],
                'theme' => $themeInfo
            ];
        });

        return $this->json($streamerInfo);
    }
}
