<?php

namespace Schnittstabil\Psr7\Csrf\Middlewares;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Schnittstabil\Psr7\Csrf\RequestAttributesTrait;

/**
 * Middleware for whitelisting certain HTTP methods.
 */
class AcceptMethods
{
    use RequestAttributesTrait;

    /**
     * HTTP methods allowed to bypass CSRF protection.
     *
     * @var string[]
     */
    protected $methods;

    /**
     * Create new AcceptMethods middleware.
     *
     * @param string[] $methods HTTP methods allowed to bypass CSRF protection
     */
    public function __construct(array $methods = array('GET', 'OPTIONS'))
    {
        $this->methods = array_map('strtoupper', $methods);
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
        if (in_array($request->getMethod(), $this->methods)) {
            $request = $request->withAttribute(self::$isValidAttribute, true);
        }

        return $next($request, $response);
    }
}
