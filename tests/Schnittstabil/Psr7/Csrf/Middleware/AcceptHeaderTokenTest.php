<?php

namespace Schnittstabil\Psr7\Csrf\Middlewares;

require_once __DIR__.'/../MockFactory.php';

use Psr\Http\Message\ResponseInterface;
use Schnittstabil\Get;
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

        $this->assertSame(false, Get::value($isValidAttribute, $request->attributes, false));
        $this->assertSame(['oldViolation', 'newViolation1', 'newViolation2'], Get::value($violationsAttribute, $request->attributes, []));
    }
}
