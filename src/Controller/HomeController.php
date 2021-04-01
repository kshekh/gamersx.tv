<?php
namespace App\Controller;

use App\Service\TwitchApi;
use App\Entity\HomeRow;
use App\Entity\HomeRowItem;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Common\Collections\{ ArrayCollection, Criteria };
use Doctrine\Common\Collections\Expr\Comparison;

class HomeController extends AbstractController
{
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
    public function apiHome(TwitchApi $twitch): Response
    {
        $rows = $this->getDoctrine()->getRepository(HomeRow::class)
            ->findBy([], ['sortIndex' => 'ASC']);

        $rowChannels = Array();
        foreach ($rows as $row) {
            $thisRow = Array();
            $thisRow['title'] = $row->getTitle();
            $thisRow['sortIndex'] = $row->getSortIndex();
            $thisRow['itemType'] = $row->getItemType();
            $thisRow['itemSortType'] = $row->getItemSortType();

            // Set up all the HttpClient and API requests concurrently
            if ($row->getItemType() === HomeRow::ITEM_TYPE_STREAMER) {
                $streamerIds = $row->getItems()->map( function ($item) {
                    return $item->getTwitchId();
                })->toArray();

                $infos = $twitch->getStreamerInfo($streamerIds);
                $broadcasts = $twitch->getStreamForStreamer($streamerIds);

            } elseif ($row->getItemType() === HomeRow::ITEM_TYPE_GAME) {
                $gameIds = $row->getItems()->map( function ($item) {
                    return $item->getTwitchId();
                })->toArray();

                $infos = $twitch->getGameInfo($gameIds);
                $broadcasts = $twitch->getTopLiveBroadcastForGame($gameIds, 60);

            } elseif ($row->getItemType() === HomeRow::ITEM_TYPE_POPULAR) {
                $options = $row->getOptions();
                $numEmbeds = $options['numEmbeds'];
                $gameIds = $options['filter']['twitchId'];

                $infos = $twitch->getGameInfo($gameIds);
                $broadcasts = $twitch->getTopLiveBroadcastForGame($gameIds, $numEmbeds);

            }

            $infos = new ArrayCollection($infos->toArray()['data']);
            $broadcasts = new ArrayCollection($broadcasts->toArray()['data']);

            if (in_array($row->getItemType(), [HomeRow::ITEM_TYPE_GAME, HomeRow::ITEM_TYPE_STREAMER])) {
                $thisRow['channels'] = $this->getChannelsForRow($row, $infos, $broadcasts);

            } elseif ($row->getItemType() === HomeRow::ITEM_TYPE_POPULAR) {
                $thisRow['channels'] = $this->getChannelsForPopularRow($row, $infos, $broadcasts);
            }

            $rowChannels[] = $thisRow;
        }

        return $this->json([
            'settings' => [
                'rows' => $rowChannels
            ]
        ]);
    }

    /**
     * This helper function gets the Channel objects for rows with game or streamer tiles
     */
    private function getChannelsForRow($row, $infos, $broadcasts) {
        $channels = Array();
        foreach ($row->getItems() as $item) {
            $twitchId = $item->getTwitchId();

            $info = $infos->filter(function($info) use ($twitchId) {
                return $info['id'] === $twitchId;
            })->first();

            if ($item->getItemType() === HomeRow::ITEM_TYPE_STREAMER) {
                $expr = new Comparison('user_id', '=', $twitchId);
            } elseif ($item->getItemType() === HomeRow::ITEM_TYPE_GAME || $item->getItemType === HomeRow::ITEM_TYPE_POPULAR) {
                $expr = new Comparison('game_id', '=', $twitchId);
            }

            $criteria = new Criteria();
            $criteria->where($expr)->getFirstResult();
            $broadcast = $broadcasts->matching($criteria);

            if (!$broadcast->isEmpty()) {
                $broadcast = $broadcast->first();
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
