<?php

namespace App\Controller;

use App\Entity\HomeRow;
use App\Containerizer\ContainerizerFactory;
use App\Service\HomeRowInfo;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class HomeController extends AbstractController
{
    private $homeRowInfo;

    public function __construct(HomeRowInfo $homeRowInfo)
    {
        $this->homeRowInfo = $homeRowInfo;
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
    public function apiHome(CacheInterface $gamersxCache, ContainerizerFactory $containerizer): Response
    {
        $cache = new FilesystemAdapter();
        $beta = 1.0;
        $rowChannels = $cache->get('home', function (ItemInterface $item) use ($containerizer) {
            $item->expiresAfter(240); //expire cache in every 240 sec
            $rows = $this->getDoctrine()->getRepository(HomeRow::class)
                ->findBy(['isPublished' => TRUE], ['sortIndex' => 'ASC']);

            $currentTime = $this->homeRowInfo->convertHoursMinutesToSeconds(date('H:i'));

            foreach ($rows as $row) {
                $isPublishedStartTime = $row->getIsPublishedStart();
                $isPublishedEndTime = $row->getIsPublishedEnd();

                if ($row->getIsPublished() === FALSE) {
                    continue;
                }

                if (
                    !is_null($isPublishedStartTime) && !is_null($isPublishedEndTime) &&
                    (($currentTime >= $isPublishedStartTime) && ($currentTime <= $isPublishedEndTime))
                ) {
                    $thisRow = Array();
                    $thisRow['title'] = $row->getTitle();
                    $thisRow['sortIndex'] = $row->getSortIndex();
                    $thisRow['componentName'] = $row->getLayout();

                    $containers = Array();

                    $containerized = $containerizer($row);
                    $channels = $containerized->getContainers();

                    foreach ($channels as $key => $channel) {
                        $channels[$key]['isGlowStyling'] = $row->getIsGlowStyling();
                    }

                    $thisRow['channels'] = $channels;

                    $rowChannels[] = $thisRow;
                }
            }

            return $rowChannels;
        }, $beta);
        return $this->json([
            'settings' => [
                'rows' => $rowChannels
            ]
        ]);
    }
}
