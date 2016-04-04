<?php

namespace Schnittstabil\Psr7\Csrf\Middlewares;

use Schnittstabil\Psr7\Csrf\MockFactory;

/**
 * RespondWithHeaderToken tests.
 */
class RespondWithHeaderTokenTest extends \PHPUnit_Framework_TestCase
{
    public function testMiddlewareShouldRespondWithHeader()
    {
        $request = MockFactory::createServerRequestMock($this);
        $response = MockFactory::createResponseMock($this);

        $sut = new RespondWithHeaderToken(function () {
            return 'signed.token';
        });

        $response = $sut($request, $response, function ($req, $res) {
            return $res;
        });

        $this->assertSame(['signed.token'], $response->getHeader('XSRF-TOKEN'));
    }
}
