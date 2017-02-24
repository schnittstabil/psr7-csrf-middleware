<?php

namespace Schnittstabil\Psr7\Csrf;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use function Schnittstabil\Get\getValue;

/**
 * Mock Factory.
 */
trait MockFactoryTrait
{
    /**
     * Mock MessageInterface methods.
     *
     * @param MessageInterface $message the message
     *
     * @return MessageInterface
     */
    protected function messageMixins(MessageInterface $message)
    {
        $message->headers = [];

        $message->method('withoutHeader')->will(
            $this->returnCallback(
                function ($name) use ($message) {
                    unset($message->headers[$name]);

                    return $message;
                }
            )
        );

        $message->method('getHeader')->will(
            $this->returnCallback(
                function ($name) use ($message) {
                    return getValue([$name], $message->headers, []);
                }
            )
        );

        $message->method('withAddedHeader')->will(
            $this->returnCallback(
                function ($name, $values) use ($message) {
                    if (!is_array($values)) {
                        $values = [$values];
                    }
                    $old = getValue([$name], $message->headers, []);
                    $message->headers[$name] = array_merge($old, $values);

                    return $message;
                }
            )
        );

        $message->method('withHeader')->will(
            $this->returnCallback(
                function ($name, $values) use ($message) {
                    if (!is_array($values)) {
                        $values = [$values];
                    }
                    $message->headers[$name] = $values;

                    return $message;
                }
            )
        );

        $message->method('getBody')->will(
            $this->returnCallback(
                function () use ($message) {
                    return $message->body;
                }
            )
        );

        $message->method('withBody')->will(
            $this->returnCallback(
                function ($body) use ($message) {
                    $message->body = $body;

                    return $message;
                }
            )
        );

        return $message;
    }

    /**
     * Mock ResponseInterface methods.
     *
     * @param TestCase         $testCase the test case
     * @param MessageInterface $message  the message
     *
     * @return MessageInterface
     */
    protected function responseMixins(MessageInterface $message)
    {
        $message->method('withStatus')->will(
            $this->returnCallback(
                function ($status) use ($message) {
                    $message->status = $status;

                    return $message;
                }
            )
        );

        $message->method('getStatusCode')->will(
            $this->returnCallback(
                function () use ($message) {
                    return $message->status;
                }
            )
        );
    }

    /**
     * Create a ServerRequestInterface Mock.
     *
     * @return ServerRequestInterface
     */
    public function createServerRequestMock()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $this->messageMixins($request);
        $request->attributes = [];

        $request->method('getAttribute')->will(
            $this->returnCallback(
                function ($name, $default = null) use ($request) {
                    return getValue([$name], $request->attributes, $default);
                }
            )
        );

        $request->method('withAttribute')->will(
            $this->returnCallback(
                function ($name, $value) use ($request) {
                    $request->attributes[$name] = $value;

                    return $request;
                }
            )
        );

        return $request;
    }

    /**
     * Create a ResponseInterface Mock.
     *
     * @return ResponseInterface
     */
    public function createResponseMock()
    {
        $response = $this->createMock(ResponseInterface::class);
        $this->messageMixins($response);
        $this->responseMixins($response);

        return $response;
    }
}
