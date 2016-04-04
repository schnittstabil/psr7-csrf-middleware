<?php

namespace Schnittstabil\Psr7\Csrf\Middlewares;

use Schnittstabil\Psr7\Csrf\MockFactory;
use Schnittstabil\Psr7\Csrf\RequestAttributesTrait;

/**
 * Guard tests.
 */
class GuardTest extends \PHPUnit_Framework_TestCase
{
    public function testMiddlewareShouldRejectAllRequests()
    {
        $isValidAttribute = RequestAttributesTrait::$isValidAttribute;
        $violationsAttribute = RequestAttributesTrait::$violationsAttribute;

        $request = MockFactory::createServerRequestMock($this);
        $response = MockFactory::createResponseMock($this);
        $sut = new Guard();

        $res = $sut($request, $response, function ($req, $res) {
            $this->assertTrue(false);
        });

        $this->assertContains('Forbidden', (string) $res->getBody(), '', true);
        $this->assertSame(403, $res->getStatusCode());
    }
}
