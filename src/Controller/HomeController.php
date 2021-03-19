<?php
namespace App\Controller;

use App\Service\TwitchApi;
use App\Service\RowSettings;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(ParameterBagInterface $params,
        RowSettings $settingsService, TwitchApi $twitch)
    {

        $settingsFile = $params->get("app.row_settings");
        $settings = $settingsService->getSettingsFromJsonFile($settingsFile);

        $rowChannels = Array();
        foreach ($settingsService->toEntities($settings) as $row) {
            $thisRow = Array();
            $thisRow['label'] = $row->getTitle();
            $thisRow['sort'] = $row->getSort();
            $thisRow['itemType'] = $row->getItemType();

            // Set up all the HttpClient and API requests concurrently
            if ($row->getItemType() === 'streamer') {
                $streamerIds = $row->getItems()->map( function ($item) {
                    return $item->getTwitchId();
                })->toArray();

                $channels = $twitch->getStreamerInfo($streamerIds);
                $broadcasts = $twitch->getStreamForStreamer($streamerIds);
                $thisRow['infoRequests'] = $channels;
                $thisRow['broadcastRequests'] = $broadcasts;

            } elseif ($row->getItemType() === 'game') {
                $gameIds = $row->getItems()->map( function ($item) {
                    return $item->getTwitchId();
                })->toArray();

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
            $broadcast = $row['broadcastRequests']->toArray()['data'];
            $infos = $row['infoRequests']->toArray()['data'];

            if (count($broadcast) === 0) {
                $embed = false;
                // Nobody is online right now. No embeds at all.
            } else {
                $embed = $broadcast[0];
                if ($row['itemType'] === 'streamer') {
                    $first = $embed['user_id'];
                } elseif ($row['itemType'] === 'game') {
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
                if ($row['itemType'] === 'streamer') {
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
                } elseif ($row['itemType'] === 'game') {
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
