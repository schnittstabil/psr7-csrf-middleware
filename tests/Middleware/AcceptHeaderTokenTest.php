<?php

namespace Schnittstabil\Psr7\Csrf\Middlewares;

use Psr\Http\Message\ResponseInterface;
use function Schnittstabil\Get\getValue;
use Schnittstabil\Psr7\Csrf\MockFactory;
use Schnittstabil\Psr7\Csrf\RequestAttributesTrait;

/**
 * AcceptHeaderToken tests.
 */
class AcceptHeaderTokenTest extends \PHPUnit_Framework_TestCase
{
    public function testMiddlewareShouldPreserveViolations()
    {
        $isValidAttribute = RequestAttributesTrait::$isValidAttribute;
        $violationsAttribute = RequestAttributesTrait::$violationsAttribute;

        $request = MockFactory::createServerRequestMock($this);
        $request->attributes[$isValidAttribute] = false;
        $request->attributes[$violationsAttribute] = ['oldViolation'];
        $request->headers['X-XSRF-TOKEN'] = ['1', '2'];

        $sut = new AcceptHeaderToken(function ($token) {
            return ['newViolation'.$token];
        });

        $sut($request, $this->getMock(ResponseInterface::class), function ($req, $res) {
            return $res;
        });

        $this->assertSame(false, getValue($isValidAttribute, $request->attributes, false));
        $this->assertSame(['oldViolation', 'newViolation1', 'newViolation2'], getValue($violationsAttribute, $request->attributes, []));
    }
}
