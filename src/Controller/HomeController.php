<?php
namespace App\Controller;

use App\Service\TwitchApi;
use App\Service\RowSettings;
use App\Entity\HomeRow;
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
    public function apiHome(RowSettings $settingsService, TwitchApi $twitch): Response
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
            if ($row->getItemType() === 'streamer') {
                $streamerIds = $row->getItems()->map( function ($item) {
                    return $item->getTwitchId();
                })->toArray();

                $infos = $twitch->getStreamerInfo($streamerIds);
                $broadcasts = $twitch->getStreamForStreamer($streamerIds);

            } elseif ($row->getItemType() === 'game') {
                $gameIds = $row->getItems()->map( function ($item) {
                    return $item->getTwitchId();
                })->toArray();

                $infos = $twitch->getGameInfo($gameIds);
                $broadcasts = $twitch->getTopLiveBroadcastForGame($gameIds, 60);

            }

            $infos = new ArrayCollection($infos->toArray()['data']);
            $broadcasts = new ArrayCollection($broadcasts->toArray()['data']);

            $channels = Array();
            foreach ($row->getItems() as $item) {
                $twitchId = $item->getTwitchId();

                $info = $infos->filter(function($info) use ($twitchId) {
                    return $info['id'] === $twitchId;
                })->first();

                if ($item->getItemType() === 'streamer') {
                    $image = $info['profile_image_url'];
                    $imageType = 'profile';
                    $link = '/streamer/'.$info['id'];

                    $expr = new Comparison('user_id', '=', $twitchId);
                } elseif ($item->getItemType() === 'game') {
                    $image = $info['box_art_url'];
                    $imageType = 'boxArt';
                    $link = '/game/'.$info['id'];

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
                    'rowType' => $item->getItemType(),
                    'rowName' => $item->getHomeRow()->getTitle(),
                    'sortIndex' => $item->getSortIndex(),
                    'showArt' => $item->getShowArt(),
                    'offlineDisplayType' => $item->getOfflineDisplayType(),
                    'linkType' => $item->getLinkType(),
                ];
            }
            $thisRow['channels'] = $channels;
            $rowChannels[] = $thisRow;
        }

        return $this->json([
            'settings' => [
                'rows' => $rowChannels
            ]
        ]);
    }

}
