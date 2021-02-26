<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    public function index()
    {
        $settings = [
            'rows' => [
                [
                    'name' => 'Featured',
                    'itemsType' => 'streamer',
                    'items' => [
                        'pokimane',
                        'broxh_',
                    ]
                ], [
                    'name' => 'Games',
                    'itemsType' => 'game',
                    'items' => [
                        'League of Legends',
                        'Fortnite',
                        'Valorant',
                        'Call of Duty',
                        'Just Chatting',
                    ],
                ], [
                    'name' => 'Pros',
                    'itemsType' => 'streamer',
                    'items' => [
                        'Closer',
                        '100thieves',
                        'LCS',
                    ]
                ]
            ]
        ];

        return $this->render('home/index.html.twig', [
            'settings' => $settings
        ]);
    }
}
