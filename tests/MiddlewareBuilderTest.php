<?php

namespace Schnittstabil\Psr7\Csrf;

use Schnittstabil\Psr7\Csrf\MiddlewareBuilder as CsrfMiddlewareBuilder;

/**
 * MiddlewareBuilder example tests.
 *
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 */
class MiddlewareBuilderTest extends \PHPUnit\Framework\TestCase
{
    use MockFactoryTrait;

    public function testCreateHeaderToHeaderMiddlewareShouldReturnMiddleware()
    {
        $request = $this->createServerRequestMock();
        $response = $this->createResponseMock();
        $key = 'This key is not so secret - change it!';

        $sut = CsrfMiddlewareBuilder::create($key)->buildHeaderToHeaderMiddleware();

        $response = $sut($request, $response, function ($req, $res) {
            return $res;
        });

        $responseHeaders = $response->getHeader('XSRF-TOKEN');
        $this->assertCount(1, $responseHeaders);
        $token = $responseHeaders[0];
        $this->assertNotEmpty($token);
        $this->assertSame([], $sut->getTokenService()->getConstraintViolations($token));
    }
}
