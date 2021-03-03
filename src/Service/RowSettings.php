<?php

namespace App\Service;

class RowSettings
{
    public function getSettings() {
        return [
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
                        21779, // League of Legends
                        33214, // 'Fortnite'
                        516575, // 'Valorant', // 516575
                        512710, // 'Call of Duty', // 512710
                        509658, // 'Just Chatting', // 509658
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
    }
}
