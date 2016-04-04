<?php

namespace Schnittstabil\Psr7\Csrf\Middlewares;

use Psr\Http\Message\ResponseInterface;
use function Schnittstabil\Get\getValue;
use Schnittstabil\Psr7\Csrf\MockFactory;
use Schnittstabil\Psr7\Csrf\RequestAttributesTrait;

/**
 * AcceptParsedBodyToken tests.
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class AcceptParsedBodyTokenTest extends \PHPUnit_Framework_TestCase
{
    public function testMiddlewareShouldAcceptArrays()
    {
        $isValidAttribute = RequestAttributesTrait::$isValidAttribute;
        $violationsAttribute = RequestAttributesTrait::$violationsAttribute;

        $request = MockFactory::createServerRequestMock($this);
        $request->method('getParsedBody')->willReturn(['X-XSRF-TOKEN' => '1']);

        $sut = new AcceptParsedBodyToken(function ($token) {
            return [];
        });

        $sut($request, $this->getMock(ResponseInterface::class), function ($req, $res) {
            return $res;
        });

        $this->assertSame([], getValue($violationsAttribute, $request->attributes, []));
        $this->assertSame(true, getValue($isValidAttribute, $request->attributes, false));
    }

    public function testMiddlewareShouldAcceptObjects()
    {
        $isValidAttribute = RequestAttributesTrait::$isValidAttribute;
        $violationsAttribute = RequestAttributesTrait::$violationsAttribute;

        $request = MockFactory::createServerRequestMock($this);
        $request->method('getParsedBody')->willReturn(json_decode('{"X-XSRF-TOKEN": "1"}'));

        $sut = new AcceptParsedBodyToken(function ($token) {
            return [];
        });

        $sut($request, $this->getMock(ResponseInterface::class), function ($req, $res) {
            return $res;
        });

        $this->assertSame([], getValue($violationsAttribute, $request->attributes, []));
        $this->assertSame(true, getValue($isValidAttribute, $request->attributes, false));
    }

    public function testMiddlewareShouldAcceptSimpleXMLElements()
    {
        $isValidAttribute = RequestAttributesTrait::$isValidAttribute;
        $violationsAttribute = RequestAttributesTrait::$violationsAttribute;

        $request = MockFactory::createServerRequestMock($this);
        $request->method('getParsedBody')->willReturn(simplexml_load_string('<root> <X-XSRF-TOKEN>1</X-XSRF-TOKEN></root>'));

        $sut = new AcceptParsedBodyToken(function ($token) {
            return [];
        });

        $sut($request, $this->getMock(ResponseInterface::class), function ($req, $res) {
            return $res;
        });

        $this->assertSame([], getValue($violationsAttribute, $request->attributes, []));
        $this->assertSame(true, getValue($isValidAttribute, $request->attributes, false));
    }

    public function testMiddlewareShouldPreserveViolations()
    {
        $isValidAttribute = RequestAttributesTrait::$isValidAttribute;
        $violationsAttribute = RequestAttributesTrait::$violationsAttribute;

        $request = MockFactory::createServerRequestMock($this);
        $request->attributes[$isValidAttribute] = false;
        $request->attributes[$violationsAttribute] = ['oldViolation'];
        $request->method('getParsedBody')->willReturn(['X-XSRF-TOKEN' => '1']);

        $sut = new AcceptParsedBodyToken(function ($token) {
            return ['newViolation'.$token];
        });

        $sut($request, $this->getMock(ResponseInterface::class), function ($req, $res) {
            return $res;
        });

        $this->assertSame(false, $request->attributes[$isValidAttribute]);
        $this->assertSame(['oldViolation', 'newViolation1'], $request->attributes[$violationsAttribute]);
    }
}
