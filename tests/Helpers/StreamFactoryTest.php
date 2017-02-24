<?php

namespace Schnittstabil\Psr7\Csrf\Helpers;

use Psr\Http\Message\StreamInterface;
use Slim\Http\Stream as SlimStream;
use Zend\Diactoros\Stream as ZendStream;

/**
 * StreamFactory tests.
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class StreamFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testCreateZendDiactorosStreamShouldReturnStream()
    {
        $sut = StreamFactory::createZendDiactorosStream();
        $this->assertInstanceOf(StreamInterface::class, $sut);
        $this->assertInstanceOf(ZendStream::class, $sut);
    }

    public function testCreateSlimHttpStreamShouldReturnStream()
    {
        $sut = StreamFactory::createSlimHttpStream();
        $this->assertInstanceOf(StreamInterface::class, $sut);
        $this->assertInstanceOf(SlimStream::class, $sut);
    }

    /**
     * @runInSeparateProcess
     */
    public function testGetShouldReturnSet()
    {
        $noop = function () {
        };
        StreamFactory::set($noop);
        $sut = StreamFactory::get();

        $this->assertSame($noop, $sut);
    }
}
