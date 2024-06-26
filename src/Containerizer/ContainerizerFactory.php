<?php

namespace App\Containerizer;

use App\Entity\HomeRow;
use App\Entity\HomeRowItem;
use App\Service\TwitchApi;
use App\Service\YouTubeApi;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class ContainerizerFactory
{
    private TwitchApi $twitch;
    private YouTubeApi $youtube;
    private LoggerInterface $logger;
    private UploaderHelper $uploader;
    private EntityManagerInterface $entityManager;

    public function __construct(TwitchApi $twitch, YouTubeApi $youtube, UploaderHelper $uploader,
        LoggerInterface $logger, EntityManagerInterface $entityManager)
    {
        $this->twitch = $twitch;
        $this->youtube = $youtube;
        $this->uploader = $uploader;
        $this->logger = $logger;
        $this->entityManager = $entityManager;
    }

    /*
     * The __invoke() method is called when a script tries to call an object as a function.
     * This is called when we have a method like $containerizer($row)
     */
    public function __invoke($toBeContainerized): ContainerizerInterface | null
    {
        $containerized = null;

        if ($toBeContainerized instanceof HomeRowItem) {
//            if ($toBeContainerized->getItemType() !== 'streamer' && $toBeContainerized->getItemType() !== 'game') {
//                dd($toBeContainerized->getItemType());
//            }
            switch($toBeContainerized->getItemType()) {
                case HomeRowItem::TYPE_GAME:
                    $containerized = new TwitchGameContainerizer(
                        homeRowItem: $toBeContainerized,
                        twitch: $this->twitch,
                        entityManager: $this->entityManager
                    );
                    break;
                case HomeRowItem::TYPE_STREAMER:
                    $containerized = new TwitchStreamerContainerizer(
                        homeRowItem: $toBeContainerized,
                        twitch: $this->twitch,
                        entityManager: $this->entityManager
                    );
                    break;
                case HomeRowItem::TYPE_CHANNEL:
                    $containerized = new YouTubeChannelContainerizer(
                        homeRowItem: $toBeContainerized,
                        youtube: $this->youtube,
                        entityManager: $this->entityManager
                    );
                    break;
                case HomeRowItem::TYPE_YOUTUBE:
                    $containerized = new YouTubeQueryContainerizer(
                        homeRowItem: $toBeContainerized,
                        youtube: $this->youtube
                    );
                    break;
                case HomeRowItem::TYPE_LINK:
                    $containerized = new NoEmbedContainer(
                        homeRowItem: $toBeContainerized
                    );
                    break;
                case HomeRowItem::TYPE_YOUTUBE_VIDEO:
                    $containerized = new YouTubeVideoContainerizer(
                        homeRowItem: $toBeContainerized,
                        youtube: $this->youtube,
                        entityManager: $this->entityManager
                    );
                    break;
                case HomeRowItem::TYPE_YOUTUBE_PLAYLIST:
                    $containerized = new YouTubePlayListContainerizer(
                        homeRowItem: $toBeContainerized,
                        youtube: $this->youtube
                    );
                    break;
                case HomeRowItem::TYPE_TWITCH_VIDEO:
                    dd(debug_backtrace()[1]['function'], $toBeContainerized);
                    $containerized = new TwitchVideoContainerizer(
                        homeRowItem: $toBeContainerized,
                        twitch: $this->twitch,
                        entityManager: $this->entityManager
                    );
                    break;
                default:
                    break;
            }
            $containerized->setLogger(logger: $this->logger);
            $containerized->setUploader(uploader: $this->uploader);
            return $containerized;
        } elseif ($toBeContainerized instanceof HomeRow) {
            return new HomeRowContainerizer(
                homeRow: $toBeContainerized,
                containerizer: $this
            );
        }

        return null;
    }
}
