<?php

namespace App\Controller;

use App\Entity\HomeRowItem;
use App\Entity\HomeRowItemOperation;
use App\Service\TwitchApi;
use App\Traits\ErrorLogTrait;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Route("/api", name="twitch_")
 */
class TwitchApiController extends AbstractController
{

    use ErrorLogTrait;

    private $params;
    private $session;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;
    private EntityManagerInterface $em;

    public function __construct(
        ParameterBagInterface $params,
        SessionInterface $session,
        ?TranslatorInterface $translator = null,
        UrlGeneratorInterface $urlGenerator,
        EntityManagerInterface $em
    )
    {
        $this->params = $params;
        $this->session = $session;
        if (null === $translator) {
            @trigger_error(sprintf(
                'Not passing an instance of "%s" as argument 6 to "%s()" is deprecated since'
                .' sonata-project/user-bundle 4.10 and will be not possible in version 5.0.',
                TranslatorInterface::class,
                __METHOD__
            ), \E_USER_DEPRECATED);
            $translator = new IdentityTranslator();
        }

        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;

        $this->em = $em;
    }
    /**
     * @Route("/query/streamer/{query}", name="queryStreamer")
     */
    public function channelQuery(Request $request, TwitchApi $twitch, $query)
    {
        try {
        if (!$this->isGranted('ROLE_LOCKED')) {
            return new RedirectResponse(
                $this->generateUrl('sonata_user_admin_security_login')
            );
        }
        $first = $request->get('first');
        $before = $request->get('before');
        $after = $request->get('after');

        $result = $twitch->searchChannels($query, $first, $before, $after);
        return $this->json($result->toArray());

        } catch (ClientException $th) {
            $this->log_error($th->getMessage(). " " . $th->getFile() . " " . $th->getLine(), $th->getCode(), "twitch_channel_query");
            throw $th;
        } catch (\Exception $ex) {
            $this->log_error($ex->getMessage(). " " . $ex->getFile() . " " . $ex->getLine(), $ex->getCode(), "twitch_channel_query");
            throw $ex;
        }
    }

    /**
     * @Route("/twitch-login", name="twitchLogin")
     */
    public function twitchLogin(Request $request, TwitchApi $twitch)
    {
        $redirectUri = $this->params->get('app.twitch_redirect_uri');
        $clientId = $this->params->get('app.twitch_id');
        $clientSecret = $this->params->get('app.twitch_secret');
        $code = $request->get('code');
        $state = $request->get('state');
        $sessionState = $this->session->get('twitch_state');
        $this->session->set('login_required_to_connect_twitch', false);
        $this->session->set('is_logged_in', false);
        if (isset($code) && isset($state) && $state == $sessionState) {
            $twitchLogin = $twitch->tryAndLoginWithTwitch($code, $redirectUri, $clientId, $clientSecret);
            if ($twitchLogin['status'] == 'ok') {
                $this->session->getFlashBag()->add(
                    'loggedin',
                    $this->translator->trans('sonata_user_already_authenticated', [], 'SonataUserBundle')
                );

                return new RedirectResponse($this->urlGenerator->generate('home'));
            } else {
                return new RedirectResponse($this->urlGenerator->generate('sonata_user_admin_security_login'));
            }
        }
        $twitchLoginUrl = $twitch->getLoginUrl($redirectUri,  $clientId);

        return new RedirectResponse($twitchLoginUrl);
    }

    /**
     * @Route("/query/game/{query}", name="queryGame")
     */
    public function gameQuery(Request $request, TwitchApi $twitch, $query)
    {
        try{
        if (!$this->isGranted('ROLE_LOCKED')) {
            return new RedirectResponse(
                $this->generateUrl('sonata_user_admin_security_login')
            );
        }
        $first = $request->get('first');
        $before = $request->get('before');
        $after = $request->get('after');

        $result = $twitch->searchGames($query, $first, $before, $after);
        return $this->json($result->toArray());
        } catch (ClientException $th) {
            $this->log_error($th->getMessage(). " " . $th->getFile() . " " . $th->getLine(), $th->getCode(), "twitch_game_query");
            throw $th;
        } catch (\Exception $ex) {
            $this->log_error($ex->getMessage(). " " . $ex->getFile() . " " . $ex->getLine(), $ex->getCode(), "twitch_game_query");
            throw $ex;
        }
    }

    /**
     * @Route("/stream/popular", name="popularStreams")
     */
    public function getPopularStreams(Request $request, TwitchApi $twitch)
    {
        try {
        $first = $request->get('first');
        $before = $request->get('before');
        $after = $request->get('after');

        $result = $twitch->getPopularStreams($first, $before, $after);
        return $this->json($result->toArray());
        } catch (ClientException $th) {
            $this->log_error($th->getMessage(). " " . $th->getFile() . " " . $th->getLine(), $th->getCode(), "twitch_popular_streams");
            throw $th;
        } catch (\Exception $ex) {
            $this->log_error($ex->getMessage(). " " . $ex->getFile() . " " . $ex->getLine(), $ex->getCode(), "twitch_popular_streams");
            throw $ex;
        }

    }

    /**
     * @Route("/streams/{gameId}", name="gameStreamers")
     */
    public function getGameStreamers($gameId,Request $request, TwitchApi $twitch)
    {
        try {
        $first = $request->get('first');
        $before = $request->get('before');
        $after = $request->get('after');
        $user_login = $request->get('user_login');
        $how_row_item_id = $request->get('how_row_item_id');
        $selectedStreamerArr = [];

        $getSelectedRowItemOperation =  $this->em->getRepository(HomeRowItemOperation::class)->findBy(['home_row_item'=>$how_row_item_id]);
        if(!empty($getSelectedRowItemOperation)) {
            foreach ($getSelectedRowItemOperation as $getSelectedOprData) {
                if($getSelectedOprData->getItemType() == 'streamer' || $getSelectedOprData->getItemType() == 'offline_streamer') {
                    $selectedStreamerArr[] = [
                        'id' => $getSelectedOprData->getStreamerId(),
                        'user_id' => $getSelectedOprData->getUserId(),
                        'user_name' => $getSelectedOprData->getStreamerName(),
                        'viewer_count' => $getSelectedOprData->getViewer(),
                        'is_blacklisted' => $getSelectedOprData->getIsBlacklisted(),
                        'priority' => $getSelectedOprData->getPriority(),
                        'item_type' => $getSelectedOprData->getItemType(),
                    ];
                }
            }
        }
        $result = $twitch->getTopLiveBroadcastForGame($gameId, $first, $before, $after,$user_login);
        $resultArr =  $result->toArray();

        $return['results_data'] = $resultArr;
        $return['old_selected_data'] = $selectedStreamerArr;

        return $this->json($return);
        } catch (ClientException $th) {
            $this->log_error($th->getMessage(). " " . $th->getFile() . " " . $th->getLine(), $th->getCode(), "twitch_game_streamers", $request->get('how_row_item_id'));
            throw $th;
        } catch (\Exception $ex) {
            $this->log_error($ex->getMessage(). " " . $ex->getFile() . " " . $ex->getLine(), $ex->getCode(), "twitch_game_streamers", $request->get('how_row_item_id'));
            throw $ex;
        }
    }

    /**
     * @Route("/streams/offline/{query}", name="gameOfflineStreamers")
     */
    public function getOfflineGameStreamers($query,Request $request, TwitchApi $twitch)
    {
        try {
        $first = $request->get('first');
        $before = $request->get('before');
        $after = $request->get('after');
        $how_row_item_id = $request->get('how_row_item_id');

        $resultArr = [];
        if(!empty($query) && $query != 'null') {
            $result = $twitch->getStreamerInfoByChannel($query);
            $resultArr =  $result->toArray();
        }

        $return['results_data'] = $resultArr;

        return $this->json($return);
        } catch (ClientException $th) {
            $this->log_error($th->getMessage(). " " . $th->getFile() . " " . $th->getLine(), $th->getCode(), "twitch_game_offline_streamers", $request->get('how_row_item_id'));
            throw $th;
        } catch (\Exception $ex) {
            $this->log_error($ex->getMessage(). " " . $ex->getFile() . " " . $ex->getLine(), $ex->getCode(), "twitch_game_offline_streamers", $request->get('how_row_item_id'));
            throw $ex;
        }
    }

    /**
     * @Route("/games/{query}", name="games")
     */
    public function getGames($query,Request $request, TwitchApi $twitch)
    {
        try {
        $first = $request->get('first');
        $before = $request->get('before');
        $after = $request->get('after');
        $how_row_item_id = $request->get('how_row_item_id');

        if(!empty($query) && $query != 'null') {
            $result = $twitch->getGameInfo($query);
        } else {
            $result = $twitch->getTopGames($first, $before, $after);
        }

        $getSelectedRowItemOperation =  $this->em->getRepository(HomeRowItemOperation::class)->findBy(['home_row_item'=>$how_row_item_id,'item_type'=>'game']);
        $selectedArr = [];
        if(!empty($getSelectedRowItemOperation)) {
            foreach ($getSelectedRowItemOperation as $getSelectedOprData) {
                $selectedArr[] = [
                    'id' => $getSelectedOprData->getGameId(),
                    'name' => $getSelectedOprData->getGameName(),
                    'is_blacklisted' => $getSelectedOprData->getIsBlacklisted(),
                    'is_whitelisted' => $getSelectedOprData->getIsWhitelisted(),
                ];
            }
        }

        $resultArr =  $result->toArray();
        $return['results_data'] = $resultArr;
        $return['old_selected_data'] = $selectedArr;

        return $this->json($return);
        } catch (ClientException $th) {
            $this->log_error($th->getMessage(). " " . $th->getFile() . " " . $th->getLine(), $th->getCode(), "twitch_get_games", $request->get('how_row_item_id'));
            throw $th;
        } catch (\Exception $ex) {
            $this->log_error($ex->getMessage(). " " . $ex->getFile() . " " . $ex->getLine(), $ex->getCode(), "twitch_get_games", $request->get('how_row_item_id'));
            throw $ex;
        }
    }

    /**
     * @Route("/check_unique_container", name="checkIsUniqueContainer")
     */
    public function checkIsUniqueContainer(Request $request)
    {
        try{
        $item_type = $request->get('item_type');
        $topic_id = $request->get('topic_id');
        $how_row_item_id = $request->get('how_row_item_id');

        $getHomeRowItem =  $this->em->getRepository(HomeRowItem::class)->findUniqueItem('topicId',$topic_id,$how_row_item_id);
        $return = [];
        if(!empty($getHomeRowItem)) {
            $return = ['is_unique_container'=> true];
        } else {
            $return = ['is_unique_container'=> false];
        }
        return $this->json($return);
        } catch (ClientException $th) {
            $this->log_error($th->getMessage(). " " . $th->getFile() . " " . $th->getLine(), $th->getCode(), "twitch_unique_container_check", $request->get('how_row_item_id'));
            throw $th;
        } catch (\Exception $ex) {
            $this->log_error($ex->getMessage(). " " . $ex->getFile() . " " . $ex->getLine(), $ex->getCode(), "twitch_unique_container_check", $request->get('how_row_item_id'));
            throw $ex;
        }
    }
}
