<?php

namespace App\Command;

use Symfony\Component\Cache\CacheItem;
use App\Containerizer\ContainerizerFactory;
use App\Entity\HomeRow;
use App\Service\HomeRowInfo;
use Doctrine\ORM\EntityManagerInterface;
use App\Traits\ErrorLogTrait;
use Predis\Client;
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


#[AsCommand(
    name: 'app:cache-home-page-containers',
    description: 'This command will cache the containers for home/api to save load time'
)]
class CacheHomePageContainers extends Command
{
    use ErrorLogTrait;

    private $containerizer;
    private $container;
    private $homeRowInfo;
    protected static $defaultName = 'app:cache-home-page-containers';
    protected static $defaultDescription = 'This command will cache the containers for home/api to save load time';
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

        $redisHost = $_ENV['REDIS_HOST'] ?? 'localhost'; // Fallback to 'localhost' if not set
        $redisPort = $_ENV['REDIS_PORT'] ?? 6379;       // Fallback to 6379 (default Redis port) if not set
        
        try {
            $cache = new RedisAdapter(
                new Client(['host' => $redisHost, 'port' => $redisPort]), 
                'namespace', 
                0
            );

            // Deleting old cache
            $cache->delete('home_item');

            // Retrieve or generate home_item cache
            $homeItemCache = $cache->getItem('home_item');

            if (!$homeItemCache->isHit()) {
                $rows = $this->entityManager->getRepository(HomeRow::class)
                    ->findBy(['isPublished' => TRUE], ['sortIndex' => 'ASC']);
                
                $rowChannels = [];

                foreach ($rows as $row) {
                    $isPublishedStartTime = $this->homeRowInfo->convertHoursMinutesToSeconds($row->getIsPublishedStart());
                    $isPublishedEndTime = $this->homeRowInfo->convertHoursMinutesToSeconds($row->getIsPublishedEnd());
                    $timezone = $row->getTimezone();
                    date_default_timezone_set(timezoneId: $timezone ?: 'America/Los_Angeles');
                    $currentTime = $this->homeRowInfo->convertHoursMinutesToSeconds(date('H:i'));
                    
                    if (!$row->getIsPublished()) {
                        continue;
                    }
                    
                    if (
                        !is_null($isPublishedStartTime) && !is_null($isPublishedEndTime) &&
                        (($currentTime >= $isPublishedStartTime) && ($currentTime <= $isPublishedEndTime))
                    ) {
                        $thisRow = [];
                        $thisRow['title'] = $row->getTitle();
                        $thisRow['sortIndex'] = $row->getSortIndex();
                        $thisRow['componentName'] = $row->getLayout();
                        $thisRow['onGamersXtv'] = $row->getonGamersXtv();
                        $thisRow['rowPaddingTop'] = ($row->getRowPaddingTop() != null) ? $row->getRowPaddingTop() : 0;
                        $thisRow['rowPaddingBottom'] = ($row->getRowPaddingBottom() != null) ? $row->getRowPaddingBottom() : 0;

                        $containerized = $containerizer(toBeContainerized: $row);
                        $channels = $containerized->getContainers();
                        foreach ($channels as $key => $channel) {
                            $channels[$key]['isGlowStyling'] = $row->getIsGlowStyling();
                        }

                        $thisRow['channels'] = $channels;

                        $rowChannels[] = $thisRow;
                    }
                }

                if (!$homeItemCache->isHit()) {
                    // Logic to populate $rowChannels
                    if (!empty($rowChannels)) {
                        // Set and save `home_item` cache
                        $homeItemCache->set($rowChannels);
                        
                        $cache->save($homeItemCache);
                    }
                }
            }
            
            // Use the cached `home_item` for `home`
            $homeCache = $cache->getItem('home');
            $homeCacheArr = ['home_container_refreshed_at' => date('Y-m-d H:i:s'), 'rows_data' => $homeItemCache->get()];
            $homeCache->set($homeCacheArr);
            $cache->save($homeCache);
            
            // Delete the temporary `home_item` cache
            $cache->delete('home_item');

            $io->success("Containers Cached successfully");
            return 0;
        } catch (\Exception $ex) {
            $message = $ex->getMessage() . " " . $ex->getFile() . " " . $ex->getLine();
            $this->log_error($message, "500", "home_cache_clear");
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
