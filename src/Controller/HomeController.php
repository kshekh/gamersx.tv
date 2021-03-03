<?php
namespace App\Controller;

use App\Service\TwitchApi;
use App\Service\RowSettings;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(RowSettings $rows)
    {
        $settings = $rows->getSettings();

        return $this->render('home/index.html.twig', [
            'settings' => $settings
        ]);
    }

}
