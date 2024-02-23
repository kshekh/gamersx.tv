<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class IubendaApiController extends AbstractController
{
    private $endpoints = [
        'https://www.iubenda.com/api/privacy-policy/49149225/section/data-processing-detailed-info/only-legal',
        'https://www.iubenda.com/api/privacy-policy/49149225/section/further-data/only-legal',
        'https://www.iubenda.com/api/privacy-policy/49149225/cookie-policy/section/technical-cookies/only-legal',
        'https://www.iubenda.com/api/privacy-policy/49149225/cookie-policy/section/other-types-cookies/only-legal',
    ];

    /**
     * @Route("/privacy-policy", name="privacy_policy")
     */
    public function privacyPolicy()
    {
        // Default values
        $result = [
            'content_0' => 'content_0',
            'content_1' => 'content_1',
            'content_2' => 'content_2',
            'content_3' => 'content_3'
        ];

        foreach ($this->endpoints as $key => $endpoint) {
            $response = file_get_contents($endpoint);
            $data = json_decode($response, true);
            if (isset($data['success']) && $data['success'] && isset($data['content'])) {
                $result["content_$key"] = $data['content'];
            }
        }
        dump($result); // Dump the variable to see its content
        //dump($result); exit;
        return $this->render('static/privacy_policy.html.twig', $result);
    }
}
