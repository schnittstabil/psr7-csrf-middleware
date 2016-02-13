<?php

namespace Schnittstabil\Psr7\Csrf\Middlewares;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Schnittstabil\Psr7\Csrf\RequestAttributesTrait;

/**
 * A Guard middleware.
 */
class Guard implements GuardInterface
{
    use RequestAttributesTrait;

    /**
     * Middleware used instead of `$next` for invalid requests.
     *
     * @var callable
     */
    protected $rejectMiddleware;

    /**
     * Create new Guard middleware.
     *
     * @param callable $rejectMiddleware Defaults to `new Reject()`.
     */
    public function __construct(callable $rejectMiddleware = null)
    {
        $this->rejectMiddleware = $rejectMiddleware ?: new Reject();
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
        if ($request->getAttribute(self::$isValidAttribute, false)) {
            return $next($request, $response);
        }

        return call_user_func($this->rejectMiddleware, $request, $response, $next);
    }
}
