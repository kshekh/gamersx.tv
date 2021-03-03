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
    public function index(RowSettings $rows, TwitchApi $twitch)
    {
        $settings = $rows->getSettings();

        foreach ($settings['rows'] as &$row) {
            if ($row['itemsType'] === 'game') {
                foreach ($row['items'] as &$item) {
                    $channel = $twitch->getTopLiveBroadcastForGame($item);
                    $item  = $channel->toArray()['data'][0]['user_name'];
                    $row['channels'] = $item;
                }

            }

        }

        return $this->render('home/index.html.twig', [
            'settings' => $settings
        ]);
    }

}
