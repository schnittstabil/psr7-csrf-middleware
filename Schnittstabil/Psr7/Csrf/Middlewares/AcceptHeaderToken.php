<?php

namespace Schnittstabil\Psr7\Csrf\Middlewares;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Schnittstabil\Psr7\Csrf\RequestAttributesTrait;

/**
 * Middleware for accepting CSRF tokens sent by HTTP headers.
 */
class AcceptHeaderToken
{
    use RequestAttributesTrait;

    /**
     * Used to validate tokens.
     *
     * @var callable
     */
    protected $tokenValidator;

    /**
     * Header field name.
     *
     * @var string
     */
    protected $headerName;

    /**
     * Create new AcceptHeaderToken middleware.
     *
     * @param callable $tokenValidator Used to validate tokens.
     * @param string   $headerName     Header field name.
     */
    public function __construct(callable $tokenValidator, $headerName = 'X-XSRF-TOKEN')
    {
        $this->tokenValidator = $tokenValidator;
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
        $isValid = $request->getAttribute(self::$isValidAttribute, false);
        $violations = $request->getAttribute(self::$violationsAttribute, []);

        foreach ($request->getHeader($this->headerName) as $token) {
            $tokenViolations = call_user_func($this->tokenValidator, $token);

            if (count($tokenViolations) === 0) {
                $isValid = true;
                continue;
            }

            $violations = array_merge($violations, $tokenViolations);
        }

        return $next($request
            ->withAttribute(self::$isValidAttribute, $isValid)
            ->withAttribute(self::$violationsAttribute, $violations), $response);
    }
}
