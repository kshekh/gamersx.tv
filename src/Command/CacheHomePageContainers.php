<?php

namespace App\Command;

use App\Containerizer\ContainerizerFactory;
use App\Entity\HomeRow;
use App\Service\HomeRowInfo;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\DefaultMarshaller;
use Symfony\Component\Cache\DeflateMarshaller;


#[AsCommand(
    name: 'app:cache-home-page-containers',
    description: 'This command will cache the containers for home/api to save load time'
)]
class CacheHomePageContainers extends Command
{
    private ContainerizerFactory $containerizer;
    private EntityManagerInterface $entityManager;
    private HomeRowInfo $homeRowInfo;
    private $redis_host;

    public function __construct(EntityManagerInterface $em, ContainerizerFactory $containerizer, HomeRowInfo $homeRowInfo, $redis_host)
    {
        $this->containerizer = $containerizer;
        $this->entityManager = $em;
        $this->homeRowInfo = $homeRowInfo;
        $this->redis_host = $redis_host;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('This command will cache the containers for home/api to save load time');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {


        $io = new SymfonyStyle($input, $output);
        $message = '';
        $containerizer = $this->containerizer;

        try {
            $cache = new RedisAdapter(new \Predis\Client(['host' => 'redis']), 'namespace', 0);
            // Deleting old cache
            //Previously `home` cache delete directly, now `home_item` item used to save temporary cache.
            //If `home_item` contain cache then first delete it, generate new save into it and at the end assign `home_item` into `home`
            $cache->delete('home_item');

            $beta = 1.0;
            $rowChannels = $cache->get('home_item', function (ItemInterface $item) use ($containerizer) {
                $rows = $this->entityManager->getRepository(HomeRow::class)
                    ->findBy(['isPublished' => TRUE], ['sortIndex' => 'ASC']);

//                $currentTime = $this->homeRowInfo->convertHoursMinutesToSeconds(date('H:i'));
                foreach ($rows as $row) {
                    $isPublishedStartTime = $this->homeRowInfo->convertHoursMinutesToSeconds($row->getIsPublishedStart());
                    $isPublishedEndTime = $this->homeRowInfo->convertHoursMinutesToSeconds($row->getIsPublishedEnd());
                    $timezone = $row->getTimezone();
                    date_default_timezone_set(timezoneId: $timezone ?: 'America/Los_Angeles');
                    $currentTime = $this->homeRowInfo->convertHoursMinutesToSeconds(date('H:i'));

                    if (! $row->getIsPublished()) {
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
                        $thisRow['rowPaddingTop'] = ($row->getRowPaddingTop() != null)? $row->getRowPaddingTop(): 0;
                        $thisRow['rowPaddingBottom'] = ($row->getRowPaddingBottom() != null)? $row->getRowPaddingBottom(): 0;

//                        $containers = array();
                        $containerized = $containerizer(toBeContainerized: $row);
//                        if ($row->getTitle() === 'NumberedRow') {
//                            dd($containerized);
//                        }
                        $channels = $containerized->getContainers();
                        foreach ($channels as $key => $channel) {
                            $channels[$key]['isGlowStyling'] = $row->getIsGlowStyling();
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
            $message = "Containers Cached successfully";
            $io->success($message);
            return 0;
        } catch (Exception $ex) {
            $message = 'message2: ' . $ex->getMessage() . '\n' . 'file: ' . $ex->getFile() . '\n' . 'line: ' . $ex->getLine() . '\n' . 'code: ' . $ex->getCode();
        }
        $io->error($message);
        return -1;
    }

    protected function json($data, int $status = 200, array $headers = []): JsonResponse
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $jsonContent = $serializer->serialize($data, 'json');

        return new JsonResponse($jsonContent, $status, $headers, true);
    }
}
