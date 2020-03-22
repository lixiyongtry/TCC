<?php declare(strict_types=1);

namespace Swoft\WebSocket\Server;

use ReflectionException;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Helper\ComposerJSON;
use Swoft\Server\Swoole\SwooleEvent;
use Swoft\SwoftComponent;
use Swoft\WebSocket\Server\Router\Router;
use Swoft\WebSocket\Server\Swoole\CloseListener;
use Swoft\WebSocket\Server\Swoole\HandshakeListener;
use Swoft\WebSocket\Server\Swoole\MessageListener;
use function bean;
use function dirname;
use function env;

/**
 * Class AutoLoader
 *
 * @since 2.0
 */
class AutoLoader extends SwoftComponent
{
    /**
     * @return bool
     */
    public function enable(): bool
    {
        return (bool)env('ENABLE_WS_SERVER', true);
    }

    /**
     * Get namespace and dir
     *
     * @return array
     * [
     *     namespace => dir path
     * ]
     */
    public function getPrefixDirs(): array
    {
        return [__NAMESPACE__ => __DIR__];
    }

    /**
     * Metadata information for the component.
     *
     * @return array
     * @see ComponentInterface::getMetadata()
     */
    public function metadata(): array
    {
        $jsonFile = dirname(__DIR__) . '/composer.json';

        return ComposerJSON::open($jsonFile)->getMetadata();
    }

    /**
     * @return array
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function beans(): array
    {
        return [
            'wsServer'     => [
                // 'class' => WebSocketServer::class,
                'port' => 18308,
                'on'   => [
                    // Enable http handle
                    // SwooleEvent::REQUEST   => \bean(RequestListener::class),
                    // websocket
                    SwooleEvent::HANDSHAKE => bean(HandshakeListener::class),
                    SwooleEvent::MESSAGE   => bean(MessageListener::class),
                    SwooleEvent::CLOSE     => bean(CloseListener::class),
                ]
            ],
            'wsRouter'     => [
                'class' => Router::class,
            ],
            'wsDispatcher' => [
                'class' => WsDispatcher::class,
            ],
        ];
    }
}
