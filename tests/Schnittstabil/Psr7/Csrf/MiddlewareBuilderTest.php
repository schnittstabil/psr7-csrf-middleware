<?php

namespace Schnittstabil\Psr7\Csrf;

require_once 'MockFactory.php';

use Schnittstabil\Psr7\Csrf\MiddlewareBuilder as CsrfMiddlewareBuilder;

/**
 * MiddlewareBuilder example tests.
 */
class MiddlewareBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateHeaderToHeaderMiddlewareShouldReturnMiddleware()
    {
        $request = MockFactory::createServerRequestMock($this);
        $response = MockFactory::createResponseMock($this);
        $key = 'This key is not so secret - change it!';

        $sut = CsrfMiddlewareBuilder::create($key)->buildHeaderToHeaderMiddleware();

        $response = $sut($request, $response, function ($req, $res) {
            $isValidAttribute = RequestAttributesTrait::$isValidAttribute;
            $violationsAttribute = RequestAttributesTrait::$violationsAttribute;

            return $res;
        });

        $responseHeaders = $response->getHeader('XSRF-TOKEN');
        $this->assertCount(1, $responseHeaders);
        $token = $responseHeaders[0];
        $this->assertNotEmpty($token);
        $this->assertSame([], $sut->getTokenService()->getConstraintViolations($token));
    }
}
