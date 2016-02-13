<?php

namespace Schnittstabil\Psr7\Csrf;

require_once 'SlimFixtures.php';

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\App;
use Schnittstabil\Psr7\Csrf\MiddlewareBuilder as CsrfMiddlewareBuilder;

/**
 * Middleware Slim Synchronizer Token Pattern example tests.
 */
class SlimSTPExampleTest extends \PHPUnit_Framework_TestCase
{
    public function createExampleApp()
    {
        $app = new App([
            'settings' => ['displayErrorDetails' => true],
        ]);

        /*
         * Additional dependencies:
         *
         *     composer require slim/slim
         */

        // $app = new App();

        $app->getContainer()['csrf_token_name'] = 'X-XSRF-TOKEN';

        $app->getContainer()['csrf'] = function ($c) {
            $key = 'This key is not so secret - change it!';

            return CsrfMiddlewareBuilder::create($key)
                ->buildSynchronizerTokenPatternMiddleware($c['csrf_token_name']);
        };

        $app->add('csrf');

        $app->get('/', function (RequestInterface $request, ResponseInterface $response) {
            $name = $this->csrf_token_name;
            $token = $this->csrf->getTokenService()->generate();

            // render HTML...
            $response = $response->write("<input type=\"hidden\" name=\"$name\" value=\"$token\" />");

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

        preg_match('/value="([^"]+)"/i', (string) $resGet->getBody(), $matches);
        $token = $matches[1];
        $this->assertNotNull($token);

        $appPost = $this->createExampleApp();
        $appPost->getContainer()['request'] = SlimFixtures::createRequest('POST')->withParsedBody(['X-XSRF-TOKEN' => $token]);
        $appPost->getContainer()['response'] = new \Slim\Http\Response();
        $appPost = $appPost->run(true);

        $this->assertSame('successfully POST', (string) $appPost->getBody());
        $this->assertSame(200, $appPost->getStatusCode());
    }
}
