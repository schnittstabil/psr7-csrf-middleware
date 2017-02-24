<?php

namespace Schnittstabil\Psr7\Csrf;

use Schnittstabil\Csrf\TokenService\TokenService;
use Schnittstabil\Csrf\TokenService\TokenServiceInterface;
use Schnittstabil\Psr7\Csrf\Middlewares\AcceptHeaderToken;
use Schnittstabil\Psr7\Csrf\Middlewares\AcceptMethods;
use Schnittstabil\Psr7\Csrf\Middlewares\AcceptParsedBodyToken;
use Schnittstabil\Psr7\Csrf\Middlewares\Guard;
use Schnittstabil\Psr7\Csrf\Middlewares\GuardInterface;
use Schnittstabil\Psr7\Csrf\Middlewares\RespondWithCookieToken;
use Schnittstabil\Psr7\Csrf\Middlewares\RespondWithHeaderToken;
use Schnittstabil\Psr7\MiddlewareStack\CallableMiddlewareStackTrait;
use Schnittstabil\Psr7\MiddlewareStack\MiddlewareStackInterface;

/**
 * CSRF protection middleware.
 */
class Middleware implements MiddlewareStackInterface
{
    use CallableMiddlewareStackTrait;

    protected $isGuarded;
    protected $tokenService;

    /**
     * Create a new Middleware.
     *
     * @param TokenServiceInterface $tokenService A token service
     */
    public function __construct(TokenServiceInterface $tokenService)
    {
        $this->isGuarded = false;
        $this->tokenService = $tokenService;
    }

    /**
     * Get the token service.
     *
     * @return TokenServiceInterface
     */
    public function getTokenService()
    {
        return $this->tokenService;
    }

    /**
     * Push a middleware onto the top of a new Stack instance.
     *
     * @param callable $newTopMiddleware the middleware to be pushed onto the top
     *
     * @return static the new instance
     *
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function add(callable $newTopMiddleware)
    {
        if ($this->isGuarded) {
            if ($newTopMiddleware instanceof GuardInterface) {
                throw new \RuntimeException('Invalid state: already guarded');
            }
        } else {
            if (!($newTopMiddleware instanceof GuardInterface)) {
                throw new \RuntimeException('Invalid state: not guarded');
            }
        }

        $clone = clone $this;
        $clone->isGuarded = true;

        return $clone->push($newTopMiddleware);
    }

    /**
     * Add new Guard middleware.
     *
     * @param callable $rejectMiddleware Defaults to `new Reject()`
     *
     * @return static
     */
    public function withGuard(callable $rejectMiddleware = null)
    {
        return $this->add(new Guard($rejectMiddleware));
    }

    /**
     * Add new AcceptHeaderToken middleware.
     *
     * @param string $headerName Header field name
     *
     * @return static
     */
    public function withAcceptHeaderToken($headerName = 'X-XSRF-TOKEN')
    {
        return $this->add(new AcceptHeaderToken([$this->tokenService, 'getConstraintViolations'], $headerName));
    }

    /**
     * Add new AcceptMethods middleware.
     *
     * @param string[] $methods HTTP methods allowed to bypass CSRF protection
     *
     * @return static
     */
    public function withAcceptMethods(array $methods = array('GET', 'OPTIONS'))
    {
        return $this->add(new AcceptMethods($methods));
    }

    /**
     * Add new AcceptParsedBodyToken middleware.
     *
     * @see https://github.com/schnittstabil/get Documentation of `Schnittstabil\Get\getValue`
     *
     * @param string|int|mixed[] $path a `Schnittstabil\Get\getValue` path
     *
     * @return static
     */
    public function withAcceptParsedBodyToken($path = 'X-XSRF-TOKEN')
    {
        return $this->add(new AcceptParsedBodyToken([$this->tokenService, 'getConstraintViolations'], $path));
    }

    /**
     * Add new RespondWithCookieToken middleware.
     *
     * @param string   $cookieName Cookie name
     * @param callable $modify     Allows to modify the cookie; same signature as `$this->modifyCookie`
     *
     * @return static
     */
    public function withRespondWithCookieToken($cookieName = 'XSRF-TOKEN', callable $modify = null)
    {
        return $this->add(new RespondWithCookieToken([$this->tokenService, 'generate'], $cookieName, $modify));
    }

    /**
     * Add new RespondWithHeaderToken middleware.
     *
     * @param string $headerName Header field name
     *
     * @return static
     */
    public function withRespondWithHeaderToken($headerName = 'XSRF-TOKEN')
    {
        return $this->add(new RespondWithHeaderToken([$this->tokenService, 'generate'], $headerName));
    }
}
