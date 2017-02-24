<?php

namespace Schnittstabil\Psr7\Csrf;

use Schnittstabil\Csrf\TokenService\TokenServiceInterface;
use Schnittstabil\Psr7\Csrf\Middlewares\GuardInterface;

/**
 * Middleware example tests.
 */
class MiddlewareTest extends \PHPUnit\Framework\TestCase
{
    public function testUnguardedAddShouldThrowOnNonGuard()
    {
        $sut = new Middleware($this->createMock(TokenServiceInterface::class));
        $this->expectException(\RuntimeException::class);

        $sut->add(function () {
        });
    }

    public function testGuardedAddShouldThrowOnGuard()
    {
        $sut = new Middleware($this->createMock(TokenServiceInterface::class));
        $guard1 = $this->createMock(GuardInterface::class);
        $guard2 = $this->createMock(GuardInterface::class);

        $sut = $sut->add($guard1);
        $sut = $sut->add(function () {
        });

        $this->expectException(\RuntimeException::class);
        $sut->add($guard2);
    }
}
