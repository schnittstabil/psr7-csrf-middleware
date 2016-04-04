<?php

namespace Schnittstabil\Psr7\Csrf\Middlewares;

use Dflydev\FigCookies\FigResponseCookies;
use Dflydev\FigCookies\SetCookie;
use Schnittstabil\Psr7\Csrf\MockFactory;
use Schnittstabil\Psr7\Csrf\RequestAttributesTrait;

/**
 * RespondWithCookieToken tests.
 */
class RespondWithCookieTokenTest extends \PHPUnit_Framework_TestCase
{
    public function testMiddlewareShouldRespondWithCookie()
    {
        $isValidAttribute = RequestAttributesTrait::$isValidAttribute;
        $violationsAttribute = RequestAttributesTrait::$violationsAttribute;

        $request = MockFactory::createServerRequestMock($this);
        $response = MockFactory::createResponseMock($this);

        $sut = new RespondWithCookieToken(function () {
            return 'signed.token';
        });

        $response = $sut($request, $response, function ($req, $res) {
            return $res;
        });

        $setCookie = FigResponseCookies::get($response, 'XSRF-TOKEN');

        $this->assertSame('signed.token', $setCookie->getValue());
        $this->assertSame('/', $setCookie->getPath());
    }

    public function testMiddlewareShouldAllowModifyCookie()
    {
        $isValidAttribute = RequestAttributesTrait::$isValidAttribute;
        $violationsAttribute = RequestAttributesTrait::$violationsAttribute;

        $request = MockFactory::createServerRequestMock($this);
        $response = MockFactory::createResponseMock($this);

        $response = FigResponseCookies::modify(
            $response,
            '_to.ken',
            function (SetCookie $setCookie) {
                return $setCookie
                    ->withExpires(1321009871)
                    ->withHttpOnly(true)
                    ->withPath('/foobar');
            }
        );

        $sut = new RespondWithCookieToken(
            function () {
                return 'signed.token';
            },
            '_to.ken',
            function ($request, $response, SetCookie $setCookie) {
                return $setCookie->withSecure(true);
            }
        );

        $response = $sut($request, $response, function ($req, $res) {
            return $res;
        });

        $setCookie = FigResponseCookies::get($response, '_to.ken');

        $this->assertSame('signed.token', $setCookie->getValue());
        $this->assertSame(true, $setCookie->getSecure());
        $this->assertSame(true, $setCookie->getHttpOnly());
        $this->assertSame(1321009871, $setCookie->getExpires());
        $this->assertSame('/foobar', $setCookie->getPath());
    }
}
