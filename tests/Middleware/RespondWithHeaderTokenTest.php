<?php

namespace Schnittstabil\Psr7\Csrf\Middlewares;

use Schnittstabil\Psr7\Csrf\MockFactoryTrait;

/**
 * RespondWithHeaderToken tests.
 *
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 */
class RespondWithHeaderTokenTest extends \PHPUnit\Framework\TestCase
{
    use MockFactoryTrait;

    public function testMiddlewareShouldRespondWithHeader()
    {
        $request = $this->createServerRequestMock();
        $response = $this->createResponseMock();

        $sut = new RespondWithHeaderToken(function () {
            return 'signed.token';
        });

        $response = $sut($request, $response, function ($req, $res) {
            return $res;
        });

        $this->assertSame(['signed.token'], $response->getHeader('XSRF-TOKEN'));
    }
}
