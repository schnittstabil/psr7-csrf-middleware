<?php

namespace Schnittstabil\Psr7\Csrf\Middlewares;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Guard marker interface.
 */
interface GuardInterface
{
    /**
     * Invoke middleware.
     *
     * @param ServerRequestInterface $request  request object
     * @param ResponseInterface      $response response object
     * @param callable               $next     next middleware
     *
     * @return ResponseInterface response object
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next);
}
