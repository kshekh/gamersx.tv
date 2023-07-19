<?php

namespace App\Controller;

use App\Service\TwitchApi;
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
    public function __construct(
        ParameterBagInterface $params,
        SessionInterface $session,
        ?TranslatorInterface $translator = null,
        UrlGeneratorInterface $urlGenerator
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

}
