<?php

namespace App\Controller;

use App\Service\TwitchApi;
use App\Entity\HomeRow;
use App\Entity\HomeRowItem;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Common\Collections\{ ArrayCollection, Criteria };
use Doctrine\Common\Collections\Expr\Comparison;

class HomeController extends AbstractController
{
    private $twitch;

    public function __construct(TwitchApi $twitch)
    {
        $this->twitch = $twitch;
    }

    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        return $this->render('home/index.html.twig');
    }

    /**
     * @Route("/home/api", name="home_api")
     */
    public function apiHome(CacheInterface $gamersxCache): Response
    {
        $rowChannels = $gamersxCache->get('home', function (ItemInterface $item) {

            $rows = $this->getDoctrine()->getRepository(HomeRow::class)
                ->findBy([], ['sortIndex' => 'ASC']);

            $rowChannels = Array();
            foreach ($rows as $row) {
                $thisRow = Array();
                $thisRow['title'] = $row->getTitle();
                $thisRow['sortIndex'] = $row->getSortIndex();
                $thisRow['itemType'] = $row->getItemType();
                $thisRow['itemSortType'] = $row->getItemSortType();

                $options = $row->getOptions();
                if ($options !== null && array_key_exists('numEmbeds', $options)) {
                    $numEmbeds = $options['numEmbeds'];
                } else {
                    $numEmbeds = null;
                }

                // Set up all the HttpClient and API requests concurrently
                if ($row->getItemType() === HomeRow::ITEM_TYPE_STREAMER) {
                    $streamerIds = $row->getItems()->map( function ($item) {
                        return $item->getTwitchId();
                    })->toArray();

                    $infos = $this->twitch->getStreamerInfo($streamerIds);
                    $broadcasts = $this->twitch->getStreamForStreamer($streamerIds);

                } elseif ($row->getItemType() === HomeRow::ITEM_TYPE_GAME) {
                    $gameIds = $row->getItems()->map( function ($item) {
                        return $item->getTwitchId();
                    })->toArray();

                    $infos = $this->twitch->getGameInfo($gameIds);
                    $broadcasts = $this->twitch->getTopLiveBroadcastForGame($gameIds, 60);

                } elseif ($row->getItemType() === HomeRow::ITEM_TYPE_POPULAR) {
                    $gameIds = $options['filter']['twitchId'];

                    $infos = $this->twitch->getGameInfo($gameIds);
                    $broadcasts = $this->twitch->getTopLiveBroadcastForGame($gameIds, $numEmbeds);

                }

                $infos = new ArrayCollection($infos->toArray()['data']);
                $broadcasts = new ArrayCollection($broadcasts->toArray()['data']);

                if (in_array($row->getItemType(), [HomeRow::ITEM_TYPE_GAME, HomeRow::ITEM_TYPE_STREAMER])) {
                    $channels = $this->getChannelsForRow($row, $infos, $broadcasts);

                } elseif ($row->getItemType() === HomeRow::ITEM_TYPE_POPULAR) {
                    $channels = $this->getChannelsForPopularRow($row, $infos, $broadcasts);
                }

                $thisRow['channels'] = array_slice($channels, 0, $numEmbeds);

                $rowChannels[] = $thisRow;
            }

            return $rowChannels;
        });

        return $this->json([
            'settings' => [
                'rows' => $rowChannels
            ]
        ]);
    }

    /**
     * Helper function to get the channel objects for rows with game or streamer tiles
     */
    private function getChannelsForRow($row, $infos, $broadcasts) {
        $channels = Array();
        foreach ($row->getItems() as $item) {
            $twitchId = $item->getTwitchId();

            $info = $infos->filter(function($info) use ($twitchId) {
                return $info['id'] === $twitchId;
            })->first();

            $criteria = new Criteria();

            if ($item->getItemType() === HomeRow::ITEM_TYPE_STREAMER) {
                $criteria->where(new Comparison('user_id', '=', $twitchId));

                // If there is a game filter set on a Streamer Home Row, only
                // show streamers as online if they are streaming that game
                $options = $row->getOptions();
                if ($options !== null && array_key_exists('filter', $options)) {
                    $gameId = $options['filter']['twitchId'];
                    $criteria->andWhere(new Comparison('game_id', '=', $gameId));
                }
            } elseif ($item->getItemType() === HomeRow::ITEM_TYPE_GAME) {
                $criteria->where(new Comparison('game_id', '=', $twitchId));
            }

            $broadcast = $broadcasts->matching($criteria);

            if (!$broadcast->isEmpty()) {
                $broadcast = $broadcast->first();
            } elseif ($item->getItemType() === HomeRow::ITEM_TYPE_GAME) {
                // If the broadcast is null for a game, double-check to make sure. This is unlikely and
                // probably due to a high variance of popularity in grouped games.
                $broadcast = $this->twitch->getTopLiveBroadcastForGame($twitchId)->toArray()['data'];
                if (count($broadcast) > 0) {
                    $broadcast = $broadcast[0];
                } else {
                    $broadcast = null;
                }
            } else {
                $broadcast = NULL;
            }

            $channels[] = [
                'info' => $info,
                'broadcast' => $broadcast,
                'rowType' => $row->getItemType(),
                'rowName' => $row->getTitle(),
                'sortIndex' => $item->getSortIndex(),
                'showArt' => $item->getShowArt(),
                'offlineDisplayType' => $item->getOfflineDisplayType(),
                'linkType' => $item->getLinkType(),
            ];
        }

        return $channels;

    }

    /**
     * Helper function to push a number of live channels for a single game
     * in the "Popular" Home Row
     */
    private function getChannelsForPopularRow($row, $infos, $broadcasts) {
        $channels = Array();

        $info = $infos->first();
        foreach ($broadcasts as $i => $broadcast) {
            $channels[] = [
                'info' => $info,
                'broadcast' => $broadcast,
                'rowType' => 'popular',
                'rowName' => $row->getTitle(),
                'sortIndex' => $i,
                'showArt' => false,
                'offlineDisplayType' => HomeRowItem::OFFLINE_DISPLAY_NONE,
                'linkType' => HomeRowItem::LINK_TYPE_GAMERSX,
            ];

        }

        return $channels;
    }

}
