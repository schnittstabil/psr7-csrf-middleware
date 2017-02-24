<?php

namespace Schnittstabil\Psr7\Csrf;

use Dflydev\FigCookies\FigResponseCookies;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\App;
use Schnittstabil\Psr7\Csrf\MiddlewareBuilder as CsrfMiddleware;

/**
 * MiddlewareBuilder Slim Cookie-to-Header example tests.
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class SlimCookieToHeaderMiddlewareExampleTest extends \PHPUnit\Framework\TestCase
{
    public function createExampleApp()
    {
        $app = new App([
            'settings' => ['displayErrorDetails' => true],
        ]);

        /*
         * Install dependencies:
         *
         *     composer require slim/slim
         *     composer require dflydev/fig-cookies
         */

        // $app = new App();

        $app->getContainer()['csrf'] = function ($c) {
            $key = 'This key is not so secret - change it!';
            $cookieName = 'XSRF-TOKEN';
            $headerName = 'X-XSRF-TOKEN';

            return CsrfMiddleware::create($key)->buildCookieToHeaderMiddleware($cookieName, $headerName);
        };

        $app->add('csrf');

        $app->get('/', function (RequestInterface $request, ResponseInterface $response) {
            // Render some Cookie-To-Header aware application, like AngularJS.
            return $response->write('successfully GET!');
        });

        $app->post('/', function (RequestInterface $request, ResponseInterface $response) {
            // POST, PUT, DELETE and PATCH are protected by default
            return $response->write('successfully POST');
        });

        // $app->run();

        return $app;
    }

    public function testSlimExampleShouldDemonstrateFailedAttack()
    {
        $app = $this->createExampleApp();
        $app->getContainer()['request'] = SlimFixtures::createRequest('POST');
        $app->getContainer()['response'] = new \Slim\Http\Response();
        $res = $app->run(true);

        $this->assertContains('Forbidden', (string) $res->getBody(), '', true);
        $this->assertSame(403, $res->getStatusCode());
    }

    public function testSlimExampleShouldDemonstrateValidPost()
    {
        $appGet = $this->createExampleApp();
        $appGet->getContainer()['request'] = SlimFixtures::createRequest('GET');
        $appGet->getContainer()['response'] = new \Slim\Http\Response();
        $resGet = $appGet->run(true);

        $this->assertContains('successfully GET', (string) $resGet->getBody(), '', true);
        $token = FigResponseCookies::get($resGet, 'XSRF-TOKEN', null)->getValue();
        $this->assertNotNull($token);

        $appPost = $this->createExampleApp();
        $appPost->getContainer()['request'] = SlimFixtures::createRequest('POST')->withHeader('X-XSRF-TOKEN', $token);
        $appPost->getContainer()['response'] = new \Slim\Http\Response();
        $appPost = $appPost->run(true);

        $this->assertSame('successfully POST', (string) $appPost->getBody());
        $this->assertSame(200, $appPost->getStatusCode());
    }
}
