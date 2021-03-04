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
                    'sort' => 'desc',
                    'display' => 'showEmbeds',
                    'items' => [
                        [
                            'id' => 44445592,
                            'label' => 'pokimane',
                        ], [
                            'id' => 105533253,
                            'label' => 'brokh_',
                        ]
                    ]
                ], [
                    'name' => 'Games',
                    'itemsType' => 'game',
                    'sort' => 'desc',
                    'display' => 'showFirstEmbed',
                    'linkType' => 'gamersx',
                    'items' => [
                        [
                            'id' => 21779,
                            'label' => 'League of Legends',
                        ], [
                            'id' => 33214,
                            'label' => 'Fortnite',
                        ], [
                            'id' => 516575,
                            'label' => 'Valorant',
                        ], [
                            'id' => 512710,
                            'label' => 'Call of Duty',
                        ], [
                            'id' => 509658,
                            'label' => 'Just Chatting',
                        ]
                    ],
                ], [
                    'name' => 'Pros',
                    'itemsType' => 'streamer',
                    'sort' => 'desc',
                    'display' => 'showFirstEmbed',
                    'linkType' => 'gamersx',
                    'items' => [
                        [
                            'id' => 160677372,
                            'label' => 'Closer',
                        ], [
                            'id' => 195450890,
                            'label' => '100thieves',
                        ], [
                            'id' => 124420521,
                            'label' => 'LCS',
                        ]
                    ]
                ]
            ]
        ];
    }
}
