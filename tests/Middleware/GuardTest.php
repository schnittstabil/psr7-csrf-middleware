<?php

namespace Schnittstabil\Psr7\Csrf\Middlewares;

use Schnittstabil\Psr7\Csrf\MockFactoryTrait;

/**
 * Guard tests.
 */
class GuardTest extends \PHPUnit\Framework\TestCase
{
    use MockFactoryTrait;

    public function testMiddlewareShouldRejectAllRequests()
    {
        $request = $this->createServerRequestMock();
        $response = $this->createResponseMock();
        $sut = new Guard();

        $res = $sut($request, $response, function () {
            $this->assertTrue(false);
        });

        $this->assertContains('Forbidden', (string) $res->getBody(), '', true);
        $this->assertSame(403, $res->getStatusCode());
    }
}
