<?php
namespace App\Controller;

use App\Service\TwitchApi;
use App\Service\RowSettings;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(RowSettings $rows, TwitchApi $twitch)
    {
        $settings = $rows->getSettings();
        $rowChannels = Array();

        foreach ($settings->rows as $row) {
            $thisRow = Array();
            $thisRow['label'] = $row->name;
            $thisRow['sort'] = $row->sort;
            $thisRow['display'] = $row->display;
            $thisRow['itemsType'] = $row->itemsType;

            // Set up all the HttpClient and API requests concurrently
            if ($row->itemsType === 'streamer') {
                $streamerIds = array_map(function ($item) use ($twitch){
                    return $item->id;
                }, $row->items);

                $channels = $twitch->getStreamerInfo($streamerIds);
                $broadcasts = $twitch->getStreamForStreamer($streamerIds);
                $thisRow['infoRequests'] = $channels;
                $thisRow['broadcastRequests'] = $broadcasts;

            } elseif ($row->itemsType === 'game') {
                $gameIds = array_map(function ($item) {
                    return $item->id;
                }, $row->items);

                $channels = $twitch->getGameInfo($gameIds);
                $broadcasts = $twitch->getTopLiveBroadcastsForGames($gameIds);
                $thisRow['infoRequests'] = $channels;
                $thisRow['broadcastRequests'] = $broadcasts;

            }

            $rowChannels[] = $thisRow;
        }

        // Now that we've set up the info requests, see which channels are displayed and which
        // are thumbnails, etc
        foreach($rowChannels as &$row) {
            $channels = Array();
            if ($row['display'] === 'showEmbeds') {
                foreach($row['infoRequests']->toArray()['data'] as $streamer) {
                    $channels[] = [
                        'channel' => $streamer['login'],
                        'showEmbed' => TRUE,
                        'showImage' => FALSE,
                        'link' => FALSE
                    ];
                }
            } elseif ($row['display'] === 'showFirstEmbed') {
                $broadcast = $row['broadcastRequests']->toArray()['data'];
                $infos = $row['infoRequests']->toArray()['data'];

                if (count($broadcast) === 0) {
                    // Nobody is online right now. No embeds at all.

                } else {
                    $embed = $broadcast[0];
                    if ($row['itemsType'] === 'streamer') {
                        $first = $embed['user_id'];
                    } elseif ($row['itemsType'] === 'game') {
                        $first = $embed['game_id'];
                    }

                    $channels[] = [
                        'channel' => $embed['user_login'],
                        'showEmbed' => TRUE,
                        'showImage' => FALSE,
                        'link' => FALSE
                    ];
                }

                foreach($infos as $info) {
                    if ($row['itemsType'] === 'streamer') {
                        if ($embed && $info['id'] == $first) {
                            continue;
                        }
                        $channels[] = [
                            'channel' => FALSE,
                            'showEmbed' => FALSE,
                            'image' => $info['profile_image_url'],
                            'imageType' => 'profile',
                            'link' => '/streamer/'.$info['id'],
                        ];
                    } elseif ($row['itemsType'] === 'game') {
                        if ($embed && $info['id'] === $first) {
                            continue;
                        }
                        $channels[] = [
                            'channel' => FALSE,
                            'showEmbed' => FALSE,
                            'image' => $info['box_art_url'],
                            'imageType' => 'boxArt',
                            'link' => '/game/'.$info['id'],
                        ];

                    }
                }
            }

            // Don't try to process these objects into JSON
            unset($row['broadcastRequests']);
            unset($row['infoRequests']);
            $row['channels'] = $channels;
        }

        return $this->render('home/index.html.twig', [
            'settings' => [
                'rows' => $rowChannels
            ]
        ]);
    }

}
