<?php

namespace App\Controller;

use App\Entity\HomeRow;
use App\Entity\SiteSettings;
use App\Containerizer\ContainerizerFactory;
use App\Service\HomeRowInfo;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\{Response, RedirectResponse};

class HomeController extends AbstractController
{
//    private $homeRowInfo;

    public function __construct(HomeRowInfo $homeRowInfo)
    {
        $this->homeRowInfo = $homeRowInfo;
    }

    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        $row = $this->getDoctrine()->getRepository(SiteSettings::class)->findOneBy([]);

        if ($this->isGranted('ROLE_LOGIN_ALLOWED') || (isset($row) && (!$row->getDisableHomeAccess() || $row->getDisableHomeAccess() == false))) {
            return $this->render('home/index.html.twig');
        }

        return new RedirectResponse(
            $this->generateUrl('sonata_user_admin_security_login')
        );
    }

    /**
     * @Route("/home/api", name="home_api")
     */
    public function apiHome(CacheInterface $gamersxCache, ContainerizerFactory $containerizer): Response
    {
        $cache = new FilesystemAdapter();

        $rowChannels = $cache->getItem('home');

        return $this->json([
            'settings' => [
                'rows' => $rowChannels->get()
            ]
        ]);
    }

    /**
     * @Route("/home/api/cache", name="home_api_cache")
     */
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
