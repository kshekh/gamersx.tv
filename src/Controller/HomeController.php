<?php

namespace App\Controller;

use App\Entity\HomeRow;
use App\Entity\SiteSettings;
use App\Containerizer\ContainerizerFactory;
use App\Service\HomeRowInfo;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\{Response, RedirectResponse};

class HomeController extends AbstractController
{
//    private $homeRowInfo;
    private $session;

    public function __construct(HomeRowInfo $homeRowInfo, SessionInterface $session)
    {
        $this->homeRowInfo = $homeRowInfo;
        $this->session = $session;
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
     * @Route("/home/rows/api", name="home_cache_api")
     */
    public function apiHomeRows(): Response
    {
        $cache = new FilesystemAdapter();
        $rowChannels = $cache->getItem('home');
        return $this->json([
            'settings' => [
                'rows' => array_column($rowChannels->get()??[],"componentName")
            ]
        ]);
    }

    /**
     * @Route("/home/sessions/api", name="home_session_api")
     */
    public function apiSessions(): Response
    {
        $isLoggedIn = $this->session->get('is_logged_in');
        $isRequiredToLoginTwitch = $this->session->get('login_required_to_connect_twitch');
        return $this->json([
            'isLoggedIn' => $isLoggedIn,
            'isRequiredToLoginTwitch' => $isRequiredToLoginTwitch
        ]);
    }
}
