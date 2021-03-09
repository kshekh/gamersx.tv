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
        $streamer = $twitch->getStreamerInfo($id);
        return $this->render('streamer/index.html.twig', [
            'info' => $streamer->toArray()['data'][0]
        ]);
    }
}
