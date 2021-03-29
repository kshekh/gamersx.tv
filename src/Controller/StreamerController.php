<?php

namespace App\Controller;

use App\Service\TwitchApi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
    public function apiStreamer(TwitchApi $twitch, $id): Response
    {
        $streamer = $twitch->getStreamerInfo($id);
        $videos = $twitch->getVideosForStreamer($id);

        return $this->json([
            'info' => $streamer->toArray()['data'][0],
            'vods' => $videos->toArray()['data']
        ]);
    }
}
