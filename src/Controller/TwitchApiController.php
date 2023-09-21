<?php

namespace App\Controller;

use App\Entity\HomeRowItem;
use App\Entity\HomeRowItemOperation;
use App\Service\TwitchApi;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Route("/api", name="twitch_")
 */
class TwitchApiController extends AbstractController
{
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
    }

    /**
     * @Route("/stream/popular", name="popularStreams")
     */
    public function getPopularStreams(Request $request, TwitchApi $twitch)
    {
        $first = $request->get('first');
        $before = $request->get('before');
        $after = $request->get('after');

        $result = $twitch->getPopularStreams($first, $before, $after);
        return $this->json($result->toArray());
    }

    /**
     * @Route("/streams/{gameId}", name="gameStreamers")
     */
    public function getGameStreamers($gameId,Request $request, TwitchApi $twitch)
    {
        $first = $request->get('first');
        $before = $request->get('before');
        $after = $request->get('after');
        $how_row_item_id = $request->get('how_row_item_id');
        $getSelectedRowItemOperation =  $this->em->getRepository(HomeRowItemOperation::class)->findBy(['home_row_item'=>$how_row_item_id]);
        $selectedStreamerArr = [];
        if(!empty($getSelectedRowItemOperation)) {
            foreach ($getSelectedRowItemOperation as $getSelectedOprData) {
                $selectedStreamerArr[$getSelectedOprData->getStreamerId()] = [
                    'is_whitelisted' => $getSelectedOprData->getIsWhitelisted(),
                    'is_blacklisted' => $getSelectedOprData->getIsBlacklisted(),
                    'priority' => $getSelectedOprData->getPriority()
                ];
            }
        }

        $result = $twitch->getTopLiveBroadcastForGame($gameId, $first);
        $resultArr =  $result->toArray();
        if(isset($resultArr['data'])) {
            foreach ($resultArr['data'] as $res_key => $res_data) {
                if(isset($selectedStreamerArr[$res_data['id']])) {
                    $resultArr['data'][$res_key]['is_whitelisted'] = $selectedStreamerArr[$res_data['id']]['is_whitelisted'];
                    $resultArr['data'][$res_key]['is_blacklisted'] = $selectedStreamerArr[$res_data['id']]['is_blacklisted'];
                    $resultArr['data'][$res_key]['priority'] = $selectedStreamerArr[$res_data['id']]['priority'];
                } else {
                    $resultArr['data'][$res_key]['is_whitelisted'] = NULL;
                    $resultArr['data'][$res_key]['is_blacklisted'] = NULL;
                    $resultArr['data'][$res_key]['priority'] = ($res_key+1);
                }
            }
            $priority = array_column($resultArr['data'], 'priority');
            array_multisort($priority, SORT_ASC, $resultArr['data']);
        }
        return $this->json($resultArr);
    }

    /**
     * @Route("/check_unique_container", name="checkIsUniqueContainer")
     */
    public function checkIsUniqueContainer(Request $request)
    {
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
    }
}
