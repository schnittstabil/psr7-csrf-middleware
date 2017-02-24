<?php

namespace Schnittstabil\Psr7\Csrf\Middlewares;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Schnittstabil\Psr7\Csrf\Helpers\StreamFactory;

/**
 * A 403 Access Forbidden middleware.
 */
class Reject
{
    /**
     * Used to create streams.
     *
     * @var callable
     */
    protected $streamFactory;

    /**
     * Create new Reject middleware.
     *
     * @param callable $streamFactory Defaults to `new StreamFactory`
     */
    public function __construct(callable $streamFactory = null)
    {
        $this->streamFactory = $streamFactory ?: new StreamFactory();
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
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $body = call_user_func($this->streamFactory);
        $body->write('403 Access Forbidden, bad CSRF token');

        return $response->withStatus(403)->withHeader('Content-type', 'text/plain')->withBody($body);
    }
}
