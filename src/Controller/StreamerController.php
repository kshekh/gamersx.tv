<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StreamerController extends AbstractController
{
    /**
     * @Route("/streamer/{id}", name="streamer")
     */
    public function index($id): Response
    {
        return $this->render('streamer/index.html.twig', [
            'controller_name' => 'StreamerController',
        ]);
    }
}
