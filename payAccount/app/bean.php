<?php

use Swoft\Http\Server\HttpServer;
use Swoft\Task\Swoole\TaskListener;
use Swoft\Task\Swoole\FinishListener;
use Swoft\Rpc\Client\Client as ServiceClient;
use Swoft\Rpc\Client\Pool as ServicePool;
use Swoft\Rpc\Server\ServiceServer;
use Swoft\Http\Server\Swoole\RequestListener;
use Swoft\WebSocket\Server\WebSocketServer;
use Swoft\Server\Swoole\SwooleEvent;
use Swoft\Db\Database;
use Swoft\Redis\RedisDb;

return [
    'logger'     => [
        'flushRequest' => true,
        'enable'       => false,
        'json'         => false,
    ],
    'db'         => [
        'class'    => Database::class,
        'dsn'      => 'mysql:dbname=test;host=172.17.0.3',
        'username' => 'root',
        'password' => 'swoft123456',
    ],
    'redis'      => [
        'class'    => RedisDb::class,
        'host'     => '127.0.0.1',
        'port'     => 6379,
        'database' => 0,
    ],
    'redis.pool'     => [
        'class'   => \Swoft\Redis\Pool::class,
        'redisDb' => \bean('redis'),
        'minActive'   => 100,
        'maxActive'   => 5000,
        'maxWait'     => 0,
        'maxWaitTime' => 0,
        'maxIdleTime' => 40,
    ],
    'order'       => [
        'class'   => \App\Rpc\Client\Client::class,
        'host'    => '127.0.0.1',
        'serviceName'    => 'order',
        'version'    => '1.0',
        'setting' => [
            'timeout'         => 0.5,
            'connect_timeout' => 1.0,
            'write_timeout'   => 10.0,
            'read_timeout'    => 0.5,
        ],
        'packet'  => bean('rpcClientPacket')
    ],
    'order.pool'  => [
        'class'  => \Six\Rpc\Client\Pool::class,
        'client' => bean('order'),
        'minActive'   => 100,
        'maxActive'   => 5000,
        'maxWait'     => 0,
        'maxWaitTime' => 0,
        'maxIdleTime' => 40,
    ],
    'message'       => [
        'class'   => \App\Rpc\Client\Client::class,
        'serviceName'    => 'message-system',
        'setting' => [
            'timeout'         => 0.5,
            'connect_timeout' => 3.0,
            'write_timeout'   => 10.0,
            'read_timeout'    => 0.5,
        ],
        'packet'  => bean('rpcClientPacket')
    ],
    'message.pool'  => [
        'class'  =>\Six\Rpc\Client\Pool::class,
        'client' => bean('message'),
        'minActive'   => 100,
        'maxActive'   => 5000,
        'maxWait'     => 0,
        'maxWaitTime' => 0,
        'maxIdleTime' => 40,
        'timeout'=>5
    ],
    'rabbit'      => [
        'class'    => \Six\Rabbit\Rabbit::class,
        'host'     => '127.0.0.1',
        'port'     => 5672,
        'database' => 0,
    ],
    'rabbit.pool'     => [
        'class'   => \Six\Rabbit\Pool::class,
        'client' => \bean('rabbit'),
        'minActive'   => 100,
        'maxActive'   => 1000,
        'maxWait'     => 0,
        'maxWaitTime' => 0,
        'maxIdleTime' => 40,
    ],
    'rpcServer'  => [
        'class' => ServiceServer::class,
        'port'     => 9806,
    ],
    'consulProvider'=>[
        'class'=>\App\Components\Consul\ConsulProvider::class
    ]
];
