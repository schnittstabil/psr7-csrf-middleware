<?php

namespace Schnittstabil\Psr7\Csrf\Middlewares;

use Dflydev\FigCookies\FigResponseCookies;
use Dflydev\FigCookies\SetCookie;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Middleware for sending CSRF tokens by cookies.
 */
class RespondWithCookieToken
{
    /**
     * Used to generate tokens.
     *
     * @var callable
     */
    protected $tokenGenerator;

    /**
     * Cookie name.
     *
     * @var string
     */
    protected $cookieName;

    /**
     * Additional SetCookie modifier.
     *
     * @var callable|null
     */
    protected $modify;

    /**
     * Create new RespondWithCookieToken middleware.
     *
     * @param callable $tokenGenerator Used to generate tokens.
     * @param string   $cookieName     Cookie name.
     * @param callable $modify         Allows to modify the cookie; same signature as `$this->modifyCookie`.
     */
    public function __construct(callable $tokenGenerator, $cookieName = 'XSRF-TOKEN', callable $modify = null)
    {
        $this->tokenGenerator = $tokenGenerator;
        $this->cookieName = $cookieName;
        $this->modify = $modify;
    }

    /**
     * Invoke middleware.
     *
     * @param ServerRequestInterface $request  request object
     * @param ResponseInterface      $response response object
     * @param callable               $next     next middleware
     *
     * @return ResponseInterface response object
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $response = FigResponseCookies::modify(
            $response,
            $this->cookieName,
            function (SetCookie $setCookie) use ($request, $response) {
                return $this->modifyCookie($request, $response, $setCookie);
            }
        );

        return $next($request, $response);
    }

    /**
     * Modify the `$response` cookie.
     *
     * @param ServerRequestInterface $request   request object
     * @param ResponseInterface      $response  response object
     * @param SetCookie              $setCookie the cookie to modify
     *
     * @return SetCookie the modified cookie.
     */
    protected function modifyCookie(ServerRequestInterface $request, ResponseInterface $response, SetCookie $setCookie)
    {
        $setCookie = $setCookie->withValue(call_user_func($this->tokenGenerator));

        if ($setCookie->getPath() === null) {
            $setCookie = $setCookie->withPath('/');
        }

        if ($this->modify !== null) {
            $setCookie = call_user_func($this->modify, $request, $response, $setCookie);
        }

        return $setCookie;
    }
}
