<?php

namespace App\Controller;

use App\Service\YouTubeApi;
use Google\Service\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, RedirectResponse};

#[Route('/api', name: 'youtube_')]
class YouTubeApiController extends AbstractController
{
    /**
     * @throws Exception
     */
    #[Route('/query/channel/{query}', name: 'queryChannel')]
    public function channelQuery(Request $request, YouTubeApi $youtube, $query): RedirectResponse|JsonResponse
    {

        if (!$this->isGranted('ROLE_LOCKED')) {
            return new RedirectResponse(
//                $this->generateUrl('sonata_user_admin_security_login')
                $this->generateUrl('admin')
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
     * @throws Exception
     */
    #[Route('/query/youtube/{query}', name: 'queryYoutube')]
    public function liveQuery(Request $request, YouTubeApi $youtube, $query): RedirectResponse|JsonResponse
    {

        if (!$this->isGranted('ROLE_LOCKED')) {
            return new RedirectResponse(
//                $this->generateUrl('sonata_user_admin_security_login')
                $this->generateUrl('admin')
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
