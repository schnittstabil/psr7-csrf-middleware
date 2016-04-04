<?php

namespace Schnittstabil\Psr7\Csrf;

use Schnittstabil\Csrf\TokenService\TokenServiceInterface;
use Schnittstabil\Psr7\Csrf\Middlewares\GuardInterface;

/**
 * Middleware example tests.
 */
class MiddlewareTest extends \PHPUnit_Framework_TestCase
{
    use \VladaHejda\AssertException;

    public function testUnguardedAddShouldThrowOnNonGuard()
    {
        $sut = new Middleware($this->getMock(TokenServiceInterface::class));

        $this->assertException(function () use ($sut) {
            $sut->add(function () {
            });
        }, \RuntimeException::class);
    }

    public function testGuardedAddShouldThrowOnGuard()
    {
        $sut = new Middleware($this->getMock(TokenServiceInterface::class));
        $guard1 = $this->getMock(GuardInterface::class);
        $guard2 = $this->getMock(GuardInterface::class);

        $sut = $sut->add($guard1);
        $sut = $sut->add(function () {
        });

        $this->assertException(function () use ($sut, $guard2) {
            $sut->add($guard2);
        }, \RuntimeException::class);
    }
}
