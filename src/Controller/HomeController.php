<?php

namespace App\Controller;

use App\Entity\HomeRowItem;
use App\Entity\SiteSettings;
use App\Containerizer\ContainerizerFactory;
use App\Service\HomeRowInfo;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\{RequestStack, Response, RedirectResponse};
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

class HomeController extends AbstractController
{
    private ManagerRegistry $doctrine;
    private HomeRowInfo $homeRowInfo;
    private RequestStack $requestStack;
    private $redis_host;

    public function __construct(HomeRowInfo $homeRowInfo, RequestStack $requestStack, ManagerRegistry $doctrine, $redis_host)
    {
        $this->homeRowInfo = $homeRowInfo;
        $this->doctrine = $doctrine;
        $this->requestStack = $requestStack;
        $this->redis_host = $redis_host;
    }

    #[Route('/', name: 'home')]
    public function index(): Response
    {
        $row = $this->doctrine->getRepository(SiteSettings::class)->findOneBy([]);

        if ($this->isGranted('ROLE_LOGIN_ALLOWED') || (isset($row) && (!$row->getDisableHomeAccess()))) {
            return $this->render('home/index.html.twig');
        }

        return new RedirectResponse(
//            $this->generateUrl('sonata_user_admin_security_login')
            $this->generateUrl('admin')
        );
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Route('/home/api', name: 'home_api')]
    public function apiHome(CacheInterface $gamersxCache, ContainerizerFactory $containerizer): Response
    {
//         $cache = new FilesystemAdapter(
//             $namespace = '',
//             $defaultLifetime = 0,
//             $directory = '/Users/ahmed/Herd/gamersx.tv/filesystem_cache'
//         );
        $cache = new RedisAdapter(new \Predis\Client(['host' => 'redis']), 'namespace', 0);

        $rowChannels = $cache->getItem('home');
        $home_container_refreshed_at = null;
        $rows = null;
        // get cache from home -> rows_data
        // home_container_refreshed_at managed to get time of last cache clear
        if ($rowChannels->isHit()) {
            $rowChannelsData = $rowChannels->get();
            $rows = $rowChannelsData['rows_data'] ?? null;
            $home_container_refreshed_at = $rowChannelsData['home_container_refreshed_at'] ?? null;
        }

        return $this->json([
            'settings' => [
                'rows' => $rows,
                'home_container_refreshed_at' => $home_container_refreshed_at
            ]
        ]);
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Route('/home/rows/api', name: 'home_cache_api')]
    public function apiHomeRows(): Response
    {
        $cache = new FilesystemAdapter();
        $rowChannels = $cache->getItem('home');
        $rows = [];
        // get cache from new rows_data key
        if ($rowChannels->isHit()) {
            $rowChannelsData = $rowChannels->get();
            $rows = array_column($rowChannelsData['rows_data']??[],"componentName");
        }
        return $this->json([
            'settings' => [
                'rows' => $rows
            ]
        ]);
    }

    #[Route('/home/sessions/api', name: 'home_session_api')]
    public function apiSessions(): Response
    {
        $session = $this->requestStack->getSession();
        $isLoggedIn = $session->get('is_logged_in');
        $isRequiredToLoginTwitch = $session->get('login_required_to_connect_twitch');
        return $this->json([
            'isLoggedIn' => $isLoggedIn,
            'isRequiredToLoginTwitch' => $isRequiredToLoginTwitch
        ]);
    }

    #[Route('/api/streamer-list', name: 'streamer_list_api')]
    public function streamer_list(): Response
    {
        $streamer_list = $this->doctrine->getRepository(HomeRowItem::class)->findStreamer();
        $return_data = [];
        foreach ($streamer_list as $streamer) {
            $item_type = $streamer->getItemType();
            $topic_id  = $streamer->getTopic()['topicId'];
            $username  = $streamer->getTopic()['label'];

            $return_data[] = [
                'Platform' => $item_type,
                'Username' => $username,
                'id' => $topic_id,
            ];
        }
        return $this->json([
            'data' => $return_data
        ]);
    }

//    /**
//     * @Route("/home/api/cache", name="home_api_cache")
//     */
//    public function apiHomeCache(CacheInterface $gamersxCache, ContainerizerFactory $containerizer): Response
//    {
//        $cache = new FilesystemAdapter();
//
//        // Deleting old cache
//        $cache->delete('home');
//
//        $beta = 1.0;
//        $rowChannels = $cache->get('home', function (ItemInterface $item) use ($containerizer) {
//            //$item->expiresAfter(240); //expire cache in every 240 sec
//            $rows = $this->getDoctrine()->getRepository(HomeRow::class)
//                ->findBy(['isPublished' => TRUE], ['sortIndex' => 'ASC']);
//
//            $currentTime = $this->homeRowInfo->convertHoursMinutesToSeconds(date('H:i'));
//
//            foreach ($rows as $row) {
//                $isPublishedStartTime = $row->getIsPublishedStart();
//                $isPublishedEndTime = $row->getIsPublishedEnd();
//
//                if ($row->getIsPublished() === FALSE) {
//                    continue;
//                }
//
//                if (
//                    !is_null($isPublishedStartTime) && !is_null($isPublishedEndTime) &&
//                    (($currentTime >= $isPublishedStartTime) && ($currentTime <= $isPublishedEndTime))
//                ) {
//                    $thisRow = Array();
//                    $thisRow['title'] = $row->getTitle();
//                    $thisRow['sortIndex'] = $row->getSortIndex();
//                    $thisRow['componentName'] = $row->getLayout();
//                    $thisRow['onGamersXtv'] = $row->getonGamersXtv();
//
//                    $containers = Array();
//
//                    $containerized = $containerizer($row);
//                    $channels = $containerized->getContainers();
//
//                    foreach ($channels as $key => $channel) {
//                        $channels[$key]['isGlowStyling'] = $row->getIsGlowStyling();
//                    }
//
//                    $thisRow['channels'] = $channels;
//
//                    $rowChannels[] = $thisRow;
//                }
//            }
//
//            if (isset($rowChannels)) {
//                return $rowChannels;
//            }
//        }, $beta);
//
//        return $this->json([
//            'settings' => [
//                'rows' => $rowChannels
//            ]
//        ]);
//    }
}
