<?php

namespace Schnittstabil\Psr7\Csrf;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use function Schnittstabil\Get\getValue;

/**
 * Mock Factory.
 */
class MockFactory
{
    /**
     * Mock MessageInterface methods.
     *
     * @param \PHPUnit_Framework_TestCase $testCase the test case
     * @param MessageInterface            $message  the message
     *
     * @return MessageInterface
     */
    protected static function messageMixins(\PHPUnit_Framework_TestCase $testCase, MessageInterface $message)
    {
        $message->headers = [];

        $message->method('withoutHeader')->will(
            $testCase->returnCallback(
                function ($name) use ($message) {
                    unset($message->headers[$name]);

                    return $message;
                }
            )
        );

        $message->method('getHeader')->will(
            $testCase->returnCallback(
                function ($name) use ($message) {
                    return getValue([$name], $message->headers, []);
                }
            )
        );

        $message->method('withAddedHeader')->will(
            $testCase->returnCallback(
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
            $testCase->returnCallback(
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
            $testCase->returnCallback(
                function () use ($message) {
                    return $message->body;
                }
            )
        );

        $message->method('withBody')->will(
            $testCase->returnCallback(
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
     * @param \PHPUnit_Framework_TestCase $testCase the test case
     * @param MessageInterface            $message  the message
     *
     * @return MessageInterface
     */
    protected static function responseMixins(\PHPUnit_Framework_TestCase $testCase, MessageInterface $message)
    {
        $message->method('withStatus')->will(
            $testCase->returnCallback(
                function ($status) use ($message) {
                    $message->status = $status;

                    return $message;
                }
            )
        );

        $message->method('getStatusCode')->will(
            $testCase->returnCallback(
                function () use ($message) {
                    return $message->status;
                }
            )
        );
    }

    /**
     * Create a ServerRequestInterface Mock.
     *
     * @param \PHPUnit_Framework_TestCase $testCase the test case
     *
     * @return ServerRequestInterface
     */
    public static function createServerRequestMock(\PHPUnit_Framework_TestCase $testCase)
    {
        $request = $testCase->getMock(ServerRequestInterface::class);
        self::messageMixins($testCase, $request);
        $request->attributes = [];

        $request->method('getAttribute')->will(
            $testCase->returnCallback(
                function ($name, $default = null) use ($request) {
                    return getValue([$name], $request->attributes, $default);
                }
            )
        );

        $request->method('withAttribute')->will(
            $testCase->returnCallback(
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
     * @param \PHPUnit_Framework_TestCase $testCase the test case
     *
     * @return ResponseInterface
     */
    public static function createResponseMock(\PHPUnit_Framework_TestCase $testCase)
    {
        $response = $testCase->getMock(ResponseInterface::class);
        self::messageMixins($testCase, $response);
        self::responseMixins($testCase, $response);

        return $response;
    }
}
