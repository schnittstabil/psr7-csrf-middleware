<?php

namespace Schnittstabil\Psr7\Csrf\Helpers;

use Psr\Http\Message\StreamInterface;

/**
 * The stream factory service locator.
 */
class StreamFactory
{
    protected static $factory;

    /**
     * Create a Stream.
     *
     * @throws \RuntimeException if no factory is set
     *
     * @return StreamInterface
     */
    public function __invoke()
    {
        return $this->create();
    }

    /**
     * Set the stream factory.
     *
     * @param callable $factory the new stream factory
     *
     * @return callable
     */
    public static function set(callable $factory)
    {
        return static::$factory = $factory;
    }

    /**
     * Get a stream factory.
     *
     * @throws \RuntimeException if no factory is set
     *
     * @return callable
     */
    public static function get()
    {
        if (static::$factory === null) {
            static::$factory = static::autodetectFactory();
        }

        return static::$factory;
    }

    /**
     * Create a Stream.
     *
     * @throws \RuntimeException if no factory is set
     *
     * @return StreamInterface
     */
    public static function create()
    {
        return call_user_func(static::get());
    }

    /**
     * Get a \Slim\Http\Stream factory.
     *
     * @return StreamInterface
     */
    public static function createSlimHttpStream()
    {
        return new \Slim\Http\Stream(fopen('php://temp', 'r+'));
    }

    /**
     * Get \Zend\Diactoros\Stream factory.
     *
     * @return StreamInterface
     */
    public static function createZendDiactorosStream()
    {
        return new \Zend\Diactoros\Stream('php://temp', 'r+');
    }

    /**
     * Try to autodetect a stream factory.
     *
     * @throws \RuntimeException if no factory is found
     *
     * @return StreamInterface
     */
    protected static function autodetectFactory()
    {
        if (class_exists('Slim\\Http\\Stream')) {
            return [static::class, 'createSlimHttpStream'];
        }

        // @codeCoverageIgnoreStart
        if (class_exists('Zend\\Diactoros\\Stream')) {
            return [static::class, 'createZendDiactorosStream'];
        }

        throw new \RuntimeException('No stream factory found');
        // @codeCoverageIgnoreEnd
    }
}
