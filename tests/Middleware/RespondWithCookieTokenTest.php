<?php

namespace Schnittstabil\Psr7\Csrf\Middlewares;

use Dflydev\FigCookies\FigResponseCookies;
use Dflydev\FigCookies\SetCookie;
use Schnittstabil\Psr7\Csrf\MockFactoryTrait;

/**
 * RespondWithCookieToken tests.
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 */
class RespondWithCookieTokenTest extends \PHPUnit\Framework\TestCase
{
    use MockFactoryTrait;

    public function testMiddlewareShouldRespondWithCookie()
    {
        $request = $this->createServerRequestMock();
        $response = $this->createResponseMock();

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
        $request = $this->createServerRequestMock();
        $response = $this->createResponseMock();

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
