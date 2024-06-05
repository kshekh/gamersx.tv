<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Contracts\HttpClient\ResponseInterface;

class TwitchApi
{
    const TWITCH_ID_DOMAIN = 'https://id.twitch.tv/';
    const TWITCH_API_DOMAIN = 'https://api.twitch.tv/helix/';
    private HttpClientInterface $client;
    private RequestStack $requestStack;
    private EntityManagerInterface $em;

    public function __construct(
        HttpClientInterface $twitch,
        RequestStack $requestStack,
        EntityManagerInterface $em
    )
    {
        $this->client = $twitch;
        $this->requestStack = $requestStack;
        $this->em = $em;
    }

    public function getLoginUrl($redirectUri, $clientId): string
    {
        $endpoint = self::TWITCH_ID_DOMAIN . 'oauth2/authorize';
        $session = $this->requestStack->getSession();
        $session->set('twitch_state', md5(microtime() . mt_rand()));
        $params = array(
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'user:read:email',
            'state' => $session->get('twitch_state')
        );

        return $endpoint . '?' .http_build_query($params);
    }

    public function tryAndLoginWithTwitch($code, $redirectUri, $clientId, $clientSecret): array
    {
        $accessToken = $this->getTwitchAccessToken($code, $redirectUri, $clientId, $clientSecret);
        $status = $accessToken['status'];
        $message = $accessToken['message'];

        if ($status == 'ok') {
            $clientAccessToken = $accessToken['api_data']['access_token'];
            $refreshToken = $accessToken['api_data']['refresh_token'];
            $userInfo = $this->getUserInfo($clientId, $clientAccessToken);
            $status = $userInfo['status'];
            $message = $userInfo['message'];

            if ($userInfo['status'] == 'ok' && isset($userInfo['api_data']['data'][0])) {
                $this->logUserInWithTwitch($userInfo['api_data']['data'][0], $clientAccessToken, $refreshToken);
            }
        }
        return array(
            'status' => $status,
            'message' => $message
        );
    }

    public function logUserInWithTwitch($apiUserInfo, $clientAccessToken, $refreshToken): void
    {
        $session = $this->requestStack->getSession();
        $session->set('twitch_user_info', $apiUserInfo);
        $session->set('twitch_user_info.access_token', $clientAccessToken);
        $session->set('twitch_user_info.refresh_token', $refreshToken);

        $userInfoWithId = $this->em->getRepository(User::class)
            ->findOneBy([
                'twitchUserId' => $apiUserInfo['id']
            ]);
        $userInfoWithEmail = $this->em->getRepository(User::class)
            ->findOneBy([
                'email' => $apiUserInfo['email']
            ]);

        if ($userInfoWithId || ($userInfoWithEmail && !$userInfoWithEmail->getPassword())) {
            $userId = $userInfoWithId ? $userInfoWithId->getId() : $userInfoWithEmail->getId();
            $userWithId = $this->em->getRepository(User::class)
                ->findOneBy([
                    'id' => $userId
                ]);
            $userWithId->setTwitchUserId($apiUserInfo['id']);
            $userWithId->setTwitchAccessToken($clientAccessToken);
            $userWithId->setTwitchRefreshToken($refreshToken);
            $this->em->persist($userWithId);
            $session->set('is_logged_in', true);
            $session->set('user_info', $userWithId);
        } elseif ($userInfoWithEmail && !$userInfoWithEmail->getTwitchUserId()) {
            $session->set('login_required_to_connect_twitch', true);
        } else {
            $user = new User();
            $user->setUsername($apiUserInfo['display_name']);
            $user->setEmail($apiUserInfo['email']);
            $user->setRoles(['ROLE_LOGIN_ALLOWED']);
            $user->setPassword('');
            $user->setUsername($apiUserInfo['display_name']);
            $user->setTwitchUserId($apiUserInfo['id']);
            $user->setTwitchAccessToken($clientAccessToken);
            $user->setTwitchRefreshToken($refreshToken);
            $this->em->persist($user);
            $session->set('is_logged_in', true);
            $session->set('user_info', $user);
        }
        $this->em->flush();
    }
    public function getUserInfo($clientId, $accessToken): array
    {
        $endpoint = self::TWITCH_API_DOMAIN . 'users';
        $apiParams = array(
            'endpoint' => $endpoint,
            'type' => 'GET',
            'authorization' => $this->getAuthorizationHeaders($clientId, $accessToken),
            'url_params' => array()
        );

        return $this->makeApiCall($apiParams);
    }

    public function getAuthorizationHeaders($clientId, $accessToken): array
    {
        return array(
            'Client-ID: ' . $clientId,
            'Authorization: Bearer ' . $accessToken
        );
    }
    public function getTwitchAccessToken($code, $redirectUri, $clientId, $clientSecret): array
    {
        $endpoint = self::TWITCH_ID_DOMAIN . 'oauth2/token';
        $apiParams = array(
            'endpoint' => $endpoint,
            'type' => 'POST',
            'url_params' => array(
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'code' => $code,
                'grant_type' => 'authorization_code',
                'redirect_uri' => $redirectUri
            )
        );

        return $this->makeApiCall($apiParams);
    }

    public function makeApiCall($params): array
    {
        $curlOptions = array(
            CURLOPT_URL => $params['endpoint'],
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_SSL_VERIFYPEER => TRUE,
            CURLOPT_SSL_VERIFYHOST => 2,
        );

        if (isset($params['authorization'])) {
            $curlOptions[CURLOPT_HEADER] = TRUE;
            $curlOptions[CURLOPT_HTTPHEADER] = $params['authorization'];
        }

        if ('POST' == $params['type']) {
            $curlOptions[CURLOPT_POST] = TRUE;
            $curlOptions[CURLOPT_POSTFIELDS] = http_build_query($params['url_params']);
        } elseif ('GET' == $params['type']) {
            $curlOptions[CURLOPT_URL] .= '?' . http_build_query($params['url_params']);
        }

        $ch = curl_init();
        curl_setopt_array($ch, $curlOptions);
        $apiResponse = curl_exec($ch);
        if (isset($params['authorization'])) {
            $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $apiResponseBody = substr($apiResponse, $headerSize);
            $apiResponse = json_decode($apiResponseBody, true);
        } else {
            $apiResponse = json_decode($apiResponse, true);
        }
        curl_close($ch);

        return array(
            'status' => isset($apiResponse['status']) ? 'fail' : 'ok',
            'message' => $apiResponse['message'] ?? '',
            'api_data' => $apiResponse,
            'endpoint' => $curlOptions[CURLOPT_URL],
            'url_params' => $params['url_params'],
        );
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function searchChannels($query, $first=20, $before=null, $after=null): ResponseInterface
    {
        return $this->getPaginatedQuery('/helix/search/channels', [
            'query' => $query
        ], $first, $before, $after);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function searchGames($query, $first=20, $before=null, $after=null): ResponseInterface
    {
        return $this->getPaginatedQuery('/helix/search/categories', [
            'query' => $query
        ], $first, $before, $after);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function getGameInfo($gameId): ResponseInterface
    {
        return $this->client->request('GET', '/helix/games', [
            'query' => [
                'id' => $gameId,
                'name' => $gameId
            ]
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function getStreamerInfo($streamerId): ResponseInterface
    {
        return $this->client->request('GET', '/helix/users', [
            'query' => [
                'id' => $streamerId
            ]
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function getStreamForStreamer($streamerId): ResponseInterface
    {
        return $this->client->request('GET', '/helix/streams', [
            'query' => [
                'user_id' => $streamerId,
            ]
        ]);

    }

    /**
     * @throws TransportExceptionInterface
     */
    public function getFollowersForStreamer($streamerId): ResponseInterface
    {
        return $this->client->request('GET', '/helix/users/follows', [
            'query' => [
                'first' => 1,
                'to_id' => $streamerId,
            ]
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function getPopularStreams($first=20, $before=null, $after=null): ResponseInterface
    {
        return $this->getPaginatedQuery('/helix/streams', [],
            $first, $before, $after);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function getTopLiveBroadcastForGame($gameId, $first=1, $before=null, $after=null, $user_login=null): ResponseInterface
    {
        $params['game_id'] = $gameId;
        if(!empty($user_login)) {
            $params['user_login'] = $user_login;
        }
        return $this->getPaginatedQuery('/helix/streams', $params, $first, $before, $after);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function getTopGames($first=1, $before=null, $after=null): ResponseInterface
    {
        return $this->getPaginatedQuery('/helix/games/top',[], $first, $before, $after);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function getVideosForStreamer($streamerId, $first=8, $before=null, $after=null): ResponseInterface
    {
        return $this->getPaginatedQuery('/helix/videos', [
            'user_id' => $streamerId,
        ], $first, $before, $after);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function getVideoInfo($videoId, $first=8, $before=null, $after=null): ResponseInterface
    {
        return $this->getPaginatedQuery('/helix/videos', [
            'id' => $videoId,
        ], $first, $before, $after);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function getClipInfo($videoId, $first=8, $before=null, $after=null): ResponseInterface
    {
        return $this->getPaginatedQuery('/helix/clips', [
            'id' => $videoId,
        ], $first, $before, $after);
    }
    /**
     * Helper method for API calls that use paginated queries
     * @throws TransportExceptionInterface
     */
    private function getPaginatedQuery($url, $queryParams, $first, $before, $after): ResponseInterface
    {
        $queryParams['first'] = $first;

        if ($before) {
            $queryParams['before'] = $before;
        } elseif ($after) {
            $queryParams['after'] = $after;
        }

        return $this->client->request('GET', $url, [
            'query' => $queryParams
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function getStreamerInfoByChannel($channelName): ResponseInterface
    {
        return $this->client->request('GET', '/helix/users', [
            'query' => [
                'login' => $channelName
            ]
        ]);
    }
}
