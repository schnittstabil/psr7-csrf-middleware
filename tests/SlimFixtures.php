<?php

namespace Schnittstabil\Psr7\Csrf;

use Slim\Http\Environment;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\RequestBody;
use Slim\Http\Uri;

/**
 * Fixtures for Slim.
 */
class SlimFixtures
{
    /**
     * Create a Slim Request.
     *
     * @param string $method the REQUEST_METHOD
     * @param string $script the SCRIPT_NAME
     * @param string $uri    the REQUEST_URI
     *
     * @return Request
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function createRequest($method = 'GET', $script = '/index.php', $uri = '/')
    {
        $env = Environment::mock(
            [
                'SCRIPT_NAME' => $script,
                'REQUEST_URI' => $uri,
                'REQUEST_METHOD' => $method,
                'HTTP_ACCEPT_ENCODING' => 'gzip,deflate',
                'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            ]
        );

        return new Request(
            $method,
            Uri::createFromEnvironment($env),
            Headers::createFromEnvironment($env),
            [],
            $env->all(),
            new RequestBody()
        );
    }
}
