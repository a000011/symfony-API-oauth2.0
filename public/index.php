<?php

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

use App\Request\ServerRequest;
use Symfony\Component\HttpFoundation\Request;

Request::setFactory(function (
    array $query = [],
    array $request = [],
    array $attributes = [],
    array $cookies = [],
    array $files = [],
    array $server = [],
    $content = null
) {
    return new ServerRequest(
        $query,
        $request,
        $attributes,
        $cookies,
        $files,
        $server,
        $content
    );
});

$request = Request::createFromGlobals($_GET, $_POST);

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
