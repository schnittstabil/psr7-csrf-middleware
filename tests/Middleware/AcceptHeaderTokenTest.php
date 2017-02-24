<?php

namespace Schnittstabil\Psr7\Csrf\Middlewares;

use Psr\Http\Message\ResponseInterface;
use function Schnittstabil\Get\getValue;
use Schnittstabil\Psr7\Csrf\MockFactoryTrait;
use Schnittstabil\Psr7\Csrf\RequestAttributesTrait;

/**
 * AcceptHeaderToken tests.
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 */
class AcceptHeaderTokenTest extends \PHPUnit\Framework\TestCase
{
    use MockFactoryTrait;

    public function testMiddlewareShouldPreserveViolations()
    {
        $isValidAttribute = RequestAttributesTrait::$isValidAttribute;
        $violationsAttribute = RequestAttributesTrait::$violationsAttribute;

        $request = $this->createServerRequestMock();
        $request->attributes[$isValidAttribute] = false;
        $request->attributes[$violationsAttribute] = ['oldViolation'];
        $request->headers['X-XSRF-TOKEN'] = ['1', '2'];

        $sut = new AcceptHeaderToken(function ($token) {
            return ['newViolation'.$token];
        });

        $sut($request, $this->createMock(ResponseInterface::class), function ($req, $res) {
            return $res;
        });

        $this->assertSame(false, getValue($isValidAttribute, $request->attributes, false));
        $this->assertSame(
            ['oldViolation', 'newViolation1', 'newViolation2'],
            getValue($violationsAttribute, $request->attributes, [])
        );
    }
}
