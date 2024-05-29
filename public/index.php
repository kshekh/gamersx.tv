<?php

use App\Kernel;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    if ($context['APP_DEBUG']) {
        umask(0000);

        Debug::enable();
    }

    if ($trustedProxies = $context['TRUSTED_PROXIES'] ?? false) {
        Request::setTrustedProxies(explode(',', $trustedProxies), Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_PORT | Request::HEADER_X_FORWARDED_PROTO);
    }

    if ($trustedHosts = $context['TRUSTED_HOSTS'] ?? false) {
        Request::setTrustedHosts([$trustedHosts]);
    }

    if (isset($context['AWS_ELB']) && $context['AWS_ELB'] === "1") {
        Request::setTrustedProxies(
            ['127.0.0.1', 'REMOTE_ADDR'],
            Request::HEADER_X_FORWARDED_AWS_ELB
        );
    }

    $kernel = new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);

    $request = Request::createFromGlobals();
    $response = $kernel->handle($request);
    $response->send();
    $kernel->terminate($request, $response);
};



