<?php

namespace Schnittstabil\Psr7\Csrf\Middlewares;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Schnittstabil\Get;
use Schnittstabil\Psr7\Csrf\RequestAttributesTrait;

/**
 * Middleware for accepting CSRF tokens sent by request bodies, e.g. POST.
 */
class AcceptParsedBodyToken
{
    use RequestAttributesTrait;

    /**
     * Used to validate tokens.
     *
     * @var callable
     */
    protected $tokenValidator;

    /**
     * Used to <a href="https://github.com/schnittstabil/get" target="_blank">`Get`</a> the token.
     *
     * @var string|int|mixed[]
     */
    protected $path;

    /**
     * Create new AcceptParsedBodyToken middleware.
     *
     * @param callable           $tokenValidator Used to validate tokens.
     * @param string|int|mixed[] $path           <a href="https://github.com/schnittstabil/get" target="_blank">See `Get::value` for details</a>
     */
    public function __construct(callable $tokenValidator, $path = 'X-XSRF-TOKEN')
    {
        $this->tokenValidator = $tokenValidator;
        $this->path = $path;
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
        $token = Get::value($this->path, $request->getParsedBody(), null);

        if ($token === null) {
            return $next($request, $response);
        }

        $tokenViolations = call_user_func($this->tokenValidator, $token);

        if (count($tokenViolations) === 0) {
            return $next($request->withAttribute(self::$isValidAttribute, true), $response);
        }

        $violations = $request->getAttribute(self::$violationsAttribute, []);
        $violations = array_merge($violations, $tokenViolations);

        return $next($request->withAttribute(self::$violationsAttribute, $violations), $response);
    }
}
