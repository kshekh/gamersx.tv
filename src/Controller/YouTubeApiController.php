<?php

namespace App\Controller;

use App\Service\YouTubeApi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api", name="youtube_")
 */
class YouTubeApiController extends AbstractController
{
    /**
     * @Route("/query/channel/{query}", name="queryChannel")
     */
    public function channelQuery(Request $request, YouTubeApi $youtube, $query)
    {

        if (!$this->isGranted('ROLE_LOCKED')) {
            return new RedirectResponse(
                $this->generateUrl('sonata_user_admin_security_login')
             );
        }
        $first = $request->get('first');
        $before = $request->get('before');
        $after = $request->get('after');

        $result = $youtube->searchChannels($query, $first, $before, $after);
        // dd($result);
        return $this->json($result);
    }

    /**
     * @Route("/query/youtube/{query}", name="queryYoutube")
     */
    public function liveQuery(Request $request, YouTubeApi $youtube, $query)
    {
 
        if (!$this->isGranted('ROLE_LOCKED')) {
            return new RedirectResponse(
                $this->generateUrl('sonata_user_admin_security_login')
             );
        }
        $first = $request->get('first');
        $before = $request->get('before');
        $after = $request->get('after');

        $result = $youtube->searchLiveChannels($query, $first, $before, $after);
        // dd($result);
        return $this->json($result);
    }

}
