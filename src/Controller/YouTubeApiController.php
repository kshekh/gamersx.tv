<?php

namespace App\Controller;

use App\Service\YouTubeApi;
use App\Traits\ErrorLogTrait;
use Google\Service\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, RedirectResponse};

#[Route('/api', name: 'youtube_')]
class YouTubeApiController extends AbstractController
{
    use ErrorLogTrait;
    /**
     * @throws Exception
     */
    #[Route('/query/channel/{query}', name: 'queryChannel')]
    public function channelQuery(Request $request, YouTubeApi $youtube, $query): RedirectResponse|JsonResponse
    {
        try{
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
        } catch (ClientException $th) {
            $this->log_error($th->getMessage(). " " . $th->getFile() . " " . $th->getLine(), $th->getCode(), "youtube_channel_query", null);
            throw $th;
        } catch (\Exception $ex) {
            $this->log_error($ex->getMessage(). " " . $ex->getFile() . " " . $ex->getLine(), $ex->getCode(), "youtube_channel_query", null);
            throw $ex;
        }
    }

    /**
     * @throws Exception
     */
    #[Route('/query/youtube/{query}', name: 'queryYoutube')]
    public function liveQuery(Request $request, YouTubeApi $youtube, $query): RedirectResponse|JsonResponse
    {
        try{
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
        } catch (ClientException $th) {
            $this->log_error($th->getMessage(). " " . $th->getFile() . " " . $th->getLine(), $th->getCode(), "youtube_live_query", null);
            throw $th;
        } catch (\Exception $ex) {
            $this->log_error($ex->getMessage(). " " . $ex->getFile() . " " . $ex->getLine(), $ex->getCode(), "youtube_live_query", null);
            throw $ex;
        }
    }

}
