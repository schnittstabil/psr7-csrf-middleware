<?php

namespace Schnittstabil\Psr7\Csrf\Middlewares;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Middleware for sending CSRF tokens by response headers.
 */
class RespondWithHeaderToken
{
    /**
     * Used to generate tokens.
     *
     * @var callable
     */
    protected $tokenGenerator;

    /**
     * Header field name.
     *
     * @var string
     */
    protected $headerName;

    /**
     * Create new RespondWithHeaderToken middleware.
     *
     * @param callable $tokenGenerator Used to generate tokens
     * @param string   $headerName     Header field name
     */
    public function __construct(callable $tokenGenerator, $headerName = 'XSRF-TOKEN')
    {
        $this->tokenGenerator = $tokenGenerator;
        $this->headerName = $headerName;
    }

    /**
     * Invoke middleware.
     *
     * @param ServerRequestInterface $request  request object
     * @param ResponseInterface      $response response object
     * @param callable               $next     next middleware
     *
     * @return ResponseInterface response object
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $token = call_user_func($this->tokenGenerator);

        return $next($request, $response->withHeader($this->headerName, $token));
    }
}
