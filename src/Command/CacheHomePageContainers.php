<?php

namespace App\Command;

use App\Containerizer\ContainerizerFactory;
use App\Entity\HomeRow;
use App\Service\HomeRowInfo;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


class CacheHomePageContainers extends Command
{
    private $containerizer;
    private $container;
    private $homeRowInfo;
    protected static $defaultName = 'app:cache-home-page-containers';
    protected static $defaultDescription = 'This command will cache the containers for home/api to save load time';

    public function __construct(ContainerInterface $container, ContainerizerFactory $containerizer, HomeRowInfo $homeRowInfo)
    {
        $this->containerizer = $containerizer;
        $this->container = $container;
        $this->homeRowInfo = $homeRowInfo;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription(self::$defaultDescription);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {


        $io = new SymfonyStyle($input, $output);
        $message = '';
        $containerizer = $this->containerizer;

        try {
            $cache = new FilesystemAdapter();

            // Deleting old cache
            //Previously `home` cache delete directly, now `home_item` item used to save temporary cache.
            //If `home_item` contain cache then first delete it, generate new save into it and at the end assign `home_item` into `home`
            $cache->delete('home_item');

            $beta = 1.0;
            $em = $this->container->get('doctrine')->getManager();
            $rowChannels = $cache->get('home_item', function (ItemInterface $item) use ($containerizer, $em) {
                $rows = $em->getRepository(HomeRow::class)
                    ->findBy(['isPublished' => TRUE], ['sortIndex' => 'ASC']);

//                $currentTime = $this->homeRowInfo->convertHoursMinutesToSeconds(date('H:i'));

                foreach ($rows as $row) {
                    $isPublishedStartTime = $this->homeRowInfo->convertHoursMinutesToSeconds($row->getIsPublishedStart());
                    $isPublishedEndTime = $this->homeRowInfo->convertHoursMinutesToSeconds($row->getIsPublishedEnd());
                    $timezone = $row->getTimezone();
                    date_default_timezone_set($timezone ? $timezone : 'America/Los_Angeles');
                    $currentTime = $this->homeRowInfo->convertHoursMinutesToSeconds(date('H:i'));

                    if ($row->getIsPublished() === FALSE) {
                        continue;
                    }

                    if (
                        !is_null($isPublishedStartTime) && !is_null($isPublishedEndTime) &&
                        (($currentTime >= $isPublishedStartTime) && ($currentTime <= $isPublishedEndTime))
                    ) {
                        $thisRow = array();
                        $thisRow['title'] = $row->getTitle();
                        $thisRow['sortIndex'] = $row->getSortIndex();
                        $thisRow['componentName'] = $row->getLayout();
                        $thisRow['onGamersXtv'] = $row->getonGamersXtv();

                        $containers = array();
                        $containerized = $containerizer($row);
                        $channels = $containerized->getContainers();

                        foreach ($channels as $key => $channel) {
                            $channels[$key]['isGlowStyling'] = $row->getIsGlowStyling();
                        }

                        $priority_Arr = [];
                        $without_priority_Arr = [];
                        foreach ($channels as $channels_data) {
                            if(isset($channels_data['priority'])) {
                                $priority_Arr[] = $channels_data;
                            } else{
                                $without_priority_Arr[] = $channels_data;
                            }
                        }
                        if(!empty($priority_Arr)) {
                            $options =  $row->getOptions();
                            if (array_key_exists('itemSortType', $options)) {
                                $sort = $options['itemSortType'];
                                if ($sort === HomeRow::SORT_ASC) {
                                    $priority = array_column($priority_Arr, 'priority');
//                                    $liveViewerCount = array_column($priority_Arr, 'liveViewerCount');
//                                    array_multisort($priority, SORT_ASC,$liveViewerCount, SORT_ASC, $priority_Arr);
                                    array_multisort($priority, SORT_ASC,$priority_Arr);
                                } elseif ($sort === HomeRow::SORT_DESC) {
                                    $priority = array_column($priority_Arr, 'priority');
//                                    $liveViewerCount = array_column($priority_Arr, 'liveViewerCount');
//                                    array_multisort($priority, SORT_DESC,$liveViewerCount, SORT_DESC, $priority_Arr);
                                    array_multisort($priority, SORT_DESC,$priority_Arr);
                                } elseif ($sort === HomeRow::SORT_FIXED) {
                                    $priority = array_column($priority_Arr, 'sortIndex');
                                    array_multisort($priority, SORT_ASC, $priority_Arr);
                                }
                            }
                            $channels = array_merge($priority_Arr,$without_priority_Arr);
                        }
                        $thisRow['channels'] = $channels;

                        $rowChannels[] = $thisRow;
                    }
                }

                if (isset($rowChannels)) {
                    return $rowChannels;
                }
            }, $beta);

            $homeItemCache = $cache->getItem('home_item');
            $homeCache = $cache->getItem('home');

            if ($homeItemCache->isHit()) {
                $homeItemCacheValue = $homeItemCache->get();
                $homeCacheArr = ['home_container_refreshed_at' => date('Y-m-d H:i:s'),'rows_data'=>$homeItemCacheValue];
                $homeCache->set($homeCacheArr);
                $cache->save($homeCache);
            }

            // Deleting cache
            $cache->delete('home_item');
            $message = "Containers Cached successfully";;
            $io->success($message);
            return 0;
        } catch (\Exception $ex) {
            $message = $ex->getMessage();
        }
        $io->error($message);
        return -1;
    }

    protected function json($data, int $status = 200, array $headers = [], array $context = []): JsonResponse
    {
        if ($this->container->has('serializer')) {
            $json = $this->container->get('serializer')->serialize($data, 'json', array_merge([
                'json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS,
            ], $context));

            return new JsonResponse($json, $status, $headers, true);
        }

        return new JsonResponse($data, $status, $headers);
    }
}
