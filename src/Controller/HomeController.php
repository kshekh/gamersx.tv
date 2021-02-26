<?php
namespace App\Controller;

use App\Service\TwitchApi;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        $settings = [
            'rows' => [
                [
                    'name' => 'Featured',
                    'itemsType' => 'streamer',
                    'items' => [
                        'pokimane', // 44445592
                        'broxh_', // 105533253
                    ]
                ], [
                    'name' => 'Games',
                    'itemsType' => 'game',
                    'items' => [
                        'League of Legends', // 21779
                        'Fortnite', // 509663
                        'Valorant', // 516575
                        'Call of Duty', // 512710
                        'Just Chatting', // 509658
                    ],
                ], [
                    'name' => 'Pros',
                    'itemsType' => 'streamer',
                    'items' => [
                        'Closer', // 160677372
                        '100thieves', // 195450890
                        'LCS', // 124420521
                    ]
                ]
            ]
        ];

        return $this->render('home/index.html.twig', [
            'settings' => $settings
        ]);
    }
}
