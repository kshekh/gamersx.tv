<?php

namespace App\Controller;

use App\Service\TwitchApi;
use App\Service\YouTubeApi;
use App\Service\ThemeInfo;
use App\Entity\HomeRowItem;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\CacheInterface;

class PageController extends AbstractController
{
    /**
     * @Route("/channel/{id}", name="channel")
     */
    public function channel(YouTubeApi $youtube, $id): Response
    {
        return $this->render('page/index.html.twig', [
            'dataUrl' => $this->generateUrl('channel_api', [
                'id' => $id
            ])
        ]);
    }
    /**
     * @Route("/query/{id}", name="querty")
     */
    public function query(YouTubeApi $youtube, $id): Response
    {
        return $this->render('page/index.html.twig', [
            'dataUrl' => $this->generateUrl('query_api', [
                'id' => $id
            ])
        ]);
    }

    /**
     * @Route("/channel/{id}/api", name="channel_api")
     */
    public function apiChannel(YouTubeApi $youtube, ThemeInfo $themeInfoService,
        CacheInterface $gamersxCache, $id): Response
    {
        $channelInfo = $gamersxCache->get("channel-${id}",
            function (ItemInterface $item) use ($id, $youtube, $themeInfoService) {
                $channel = $youtube->getChannelInfo($id)->getItems()[0];
                $themeInfo = $themeInfoService->getThemeInfo($id, HomeRowItem::TYPE_CHANNEL);
                $topNine = $youtube->getPopularChannelVideos($id, 9)->getItems();

                $imageInfo = $channel->getSnippet()->getThumbnails();
                $imageInfo = $imageInfo->getMedium() ? $imageInfo->getMedium() : $imageInfo->getStandard();

                $image = [
                    'url' => $imageInfo->getUrl(),
                    'class' => 'profile-pic',
                    'width' => $imageInfo->getWidth(),
                    'height' => $imageInfo->getHeight(),
                ];

                $embeds = array_map(function($embed) {
                    $title = $embed->getSnippet()->getDescription();
                    $embedData = [
                        'video' => $embed->getId()->getVideoId(),
                        'elementId' => 'embed-'.sha1($title)
                    ];

                    return [
                        'componentName' => 'EmbedContainer',
                        'embedName' => 'YouTubeEmbed',
                        'showOnline' => TRUE,
                        'onlineDisplay' => [
                            'title' => $title,
                            'showEmbed' => TRUE,
                            'showArt' => TRUE,
                        ],
                        'offlineDisplay' => [],
                        'image' => [],
                        'link' => '',
                        'embedData' => $embedData,
                    ];

                }, $topNine);

                return [
                    'topic' => [
                        'theme' => $themeInfo,
                        'image' => $image,
                        'title' => $channel->getSnippet()->getTitle(),
                        'embed' => $embeds[0],
                    ],
                    'tabs' => [
                        [
                            'name' => 'Top Videos',
                            'componentName' => 'EmbedTab',
                            'data' => [
                                'streams' => array_slice($embeds, 1, 8),
                            ],
                        ], [
                            'name' => 'About',
                            'componentName' => 'About',
                            'data' => [
                                'description' => $channel->getSnippet()->getDescription(),
                            ]
                        ]
                    ],
                ];
            });

        return $this->json($channelInfo);
    }

    /**
     * @Route("/query/{id}/api", name="query_api")
     */
    public function apiquery(YouTubeApi $youtube, ThemeInfo $themeInfoService,
        CacheInterface $gamersxCache, $id): Response
    {
        $queryInfo = $gamersxCache->get("query-${id}",
            function (ItemInterface $item) use ($id, $youtube, $themeInfoService) {
                $query = $youtube->getqueryInfo($id)->getItems();
                $themeInfo = $themeInfoService->getThemeInfo($id, HomeRowItem::TYPE_query);
                $topNine = $youtube->getPopularqueryVideos($id, 9)->getItems();

                return [
                    'info' => $query[0],
                    'streams' => $topNine,
                    // 'viewers' => $totalViewers,
                    // 'streamers' => $totalStreamers,
                    'theme' => $themeInfo
                ];
            });

        return $this->json($queryInfo);
    }

}
