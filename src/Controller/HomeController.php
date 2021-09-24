<?php

namespace App\Controller;

use App\Entity\HomeRow;
use App\Containerizer\ContainerizerFactory;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
    public function apiHome(CacheInterface $gamersxCache, ContainerizerFactory $containerizer): Response
    {
        $rowChannels = $gamersxCache->get('home', function (ItemInterface $item) use ($containerizer) {

            $rows = $this->getDoctrine()->getRepository(HomeRow::class)
                ->findBy(['isPublished' => TRUE], ['sortIndex' => 'ASC']);

            $rowChannels = Array();

            foreach ($rows as $row) {
                if ($row->getIsPublished() === FALSE) {
                    continue;
                }

                $thisRow = Array();
                $thisRow['title'] = $row->getTitle();
                $thisRow['sortIndex'] = $row->getSortIndex();
                $thisRow['componentName'] = $row->getLayout();

                $containers = Array();

                $containerized = $containerizer($row);
                $channels = $containerized->getContainers();
                $thisRow['channels'] = $channels;

                $rowChannels[] = $thisRow;
            }

            return $rowChannels;
        });

        return $this->json([
            'settings' => [
                'rows' => $rowChannels
            ]
        ]);
    }
}
