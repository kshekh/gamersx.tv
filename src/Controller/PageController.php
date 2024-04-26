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


use Symfony\Component\HttpFoundation\RedirectResponse;

class PageController extends AbstractController
{
    /**
     * @Route("/channel/{id}", name="channel")
     */
    public function channel(YouTubeApi $youtube, $id): Response
    {
       
       if (!$this->isGranted('ROLE_LOCKED')) {
            return new RedirectResponse(
                $this->generateUrl('sonata_user_admin_security_login')
             );
        }

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
        
        if (!$this->isGranted('ROLE_LOCKED')) {
            return new RedirectResponse(
                $this->generateUrl('sonata_user_admin_security_login')
             );
        }

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

                $broadcast = $youtube->getLiveChannel($id)->getItems();
                if (count($broadcast) == 1) {
                    $topicVideo = $broadcast[0];
                } else {
                    $topicVideo = $youtube->getRecentChannelVideos($id, 1)->getItems()[0];
                }

                $topEight = $youtube->getPopularChannelVideos($id, 8)->getItems();

                return [
                    'topic' => [
                        'theme' => $themeInfo,
                        'image' => $this->youtubeChannelInfoToImage($channel),
                        'title' => $channel->getSnippet()->getTitle(),
                        'embed' => $this->youtubeResultToEmbedContainer($topicVideo),
                    ],
                    'tabs' => [
                        [
                            'name' => 'Top Videos',
                            'componentName' => 'EmbedTab',
                            'data' => [
                                'streams' => array_map(array($this, 'youtubeResultToEmbedContainer'), $topEight),
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

                // Get all of our YT info
                $topLive = $youtube->searchLiveChannels($query, 1)->getItems();
                $topFour = $youtube->searchPopularVideos($query, 4)->getItems();

                $allResults = array_merge($topLive, $topFour);

                $channelIds = array_map(function($result) {
                    return $result->getSnippet()->getChannelId();
                }, $allResults);
                $channelInfos = $youtube->getChannelInfo($channelIds)->getItems();

                // Process the API results into embeds suitable for Vue templates
                $embeds = Array();
                foreach ($allResults as $result) {
                    $embed = $this->youtubeResultToEmbedContainer($result);
                    $embedInfo = array_filter($channelInfos, function($info) use ($result) {
                        return $info->getId() === $result->getSnippet()->getChannelId();
                    });
                    if (count($embedInfo) == 1) {
                        $info = array_values($embedInfo)[0];
                        $embed['image'] = $this->youtubeChannelInfoToImage($info);
                        $embed['onlineDisplay']['showArt'] = TRUE;
                    }
                    $embeds[] = $embed;
                }

                // Don't display art for the first (live) results, it's laid out differently with the theme
                $topicImage = $embeds[0]['image'];
                $embeds[0]['image'] = [];
                $embeds[0]['onlineDisplay']['showArt'] = FALSE;

                return [
                    'topic' => [
                        'theme' => $themeInfo,
                        'title' => $query,
                        'image' => $topicImage,
                        'embed' => $embeds[0],
                    ],
                    'tabs' => [
                        [
                            'name' => 'Top Videos',
                            'componentName' => 'EmbedTab',
                            'data' => [
                                'streams' => array_slice($embeds, 1, 4),
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
        $title = "<".$embed->getSnippet()->getChannelTitle() . "> " . $embed->getSnippet()->getDescription();
        return [
            'componentName' => 'EmbedContainer',
            'embedName' => 'YouTubeEmbed',
            'showOnline' => TRUE,
            'onlineDisplay' => [
                'title' => $title,
                'showEmbed' => TRUE,
                'showArt' => FALSE,
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

    private function youtubeChannelInfoToImage($info) {
        $imageInfo = $info->getSnippet()->getThumbnails();
        $imageInfo = $imageInfo->getMedium() ? $imageInfo->getMedium() : $imageInfo->getStandard();
        return [
            'url' => $imageInfo->getUrl(),
            'class' => 'profile-pic',
            'width' => $imageInfo->getWidth(),
            'height' => $imageInfo->getHeight(),
        ];

    }

    /**
     * @Route("/access-denied", name="access_denied")
     */
    public function accessDenied()
    {
        return $this->redirectToRoute('home');
    }
}
