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
     * @Route("/query/{query}", name="query")
     */
    public function query(YouTubeApi $youtube, $query): Response
    {
        return $this->render('page/index.html.twig', [
            'dataUrl' => $this->generateUrl('query_api', [
                'query' => $query
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

                $embeds = array_map(array($this, 'youtubeResultToEmbedContainer'), $topNine);

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
     * @Route("/query/{query}/api", name="query_api")
     */
    public function apiQuery(YouTubeApi $youtube, ThemeInfo $themeInfoService,
        CacheInterface $gamersxCache, $query): Response
    {
        $queryInfo = $gamersxCache->get("query-${query}",
            function (ItemInterface $item) use ($query, $youtube, $themeInfoService) {
                $themeInfo = $themeInfoService->getThemeInfo($query, HomeRowItem::TYPE_YOUTUBE);

                $topEight = $youtube->searchPopularVideos($query, 8)->getItems();
                $topEightEmbeds = array_map(array($this, 'youtubeResultToEmbedContainer'), $topEight);

                $topLive = $youtube->searchLiveChannels($query)->getItems()[0];

                $imageInfo = $topLive->getSnippet()->getThumbnails();
                $imageInfo = $imageInfo->getMedium() ? $imageInfo->getMedium() : $imageInfo->getStandard();
                $image = [
                    'url' => $imageInfo->getUrl(),
                    'class' => 'profile-pic',
                    'width' => $imageInfo->getWidth(),
                    'height' => $imageInfo->getHeight(),
                ];

                return [
                    'topic' => [
                        'theme' => $themeInfo,
                        'image' => $image,
                        'title' => $topLive->getSnippet()->getTitle(),
                        'embed' => $this->youtubeResultToEmbedContainer($topLive),
                    ],
                    'tabs' => [
                        [
                            'name' => 'Top Videos',
                            'componentName' => 'EmbedTab',
                            'data' => [
                                'streams' => $topEightEmbeds,
                            ],
                        ], [
                            'name' => 'Query Info',
                            'componentName' => 'About',
                            'data' => [
                                'description' => 'These are YouTube\'s most popular gaming videos for '.$query,
                            ]
                        ]
                    ],
                ];
            });

        return $this->json($queryInfo);
    }

    private function youtubeResultToEmbedContainer($embed) {
        return [
            'componentName' => 'EmbedContainer',
            'embedName' => 'YouTubeEmbed',
            'showOnline' => TRUE,
            'onlineDisplay' => [
                'title' => $title = $embed->getSnippet()->getDescription(),
                'showEmbed' => TRUE,
                'showArt' => TRUE,
            ],
            'offlineDisplay' => [],
            'image' => [],
            'link' => '',
            'embedData' => [
                'video' => $embed->getId()->getVideoId(),
                'elementId' => 'embed-'.sha1($title)
            ]
        ];
    }

}
