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
    'httpServer' => [
        'class'    => HttpServer::class,
        'port'     => 9510,
        'listener' => [
            'rpc' => bean('rpcServer')
        ],
        'on'       => [
            SwooleEvent::TASK   => bean(TaskListener::class),  // Enable task must task and finish event
            SwooleEvent::FINISH => bean(FinishListener::class)
        ],
        /* @see HttpServer::$setting */
        'setting'  => [
            'task_worker_num'       => 12,
            'task_enable_coroutine' => true
        ]
    ],
    'db'         => [
        'class'    => Database::class,
        'dsn'      => 'mysql:dbname=test;host=127.0.0.1',
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
        'minActive'   => 10,
        'maxActive'   => 20,
        'maxWait'     => 0,
        'maxWaitTime' => 0,
        'maxIdleTime' => 40,
        ],
    'order'       => [
        'class'   => \App\Rpc\Client\Client::class,
        'serviceName'    => 'order',
        'version'    => '1.0',
        'setting' => [
            'timeout'         => 0.5,
            'connect_timeout' => 1.0,
            'write_timeout'   => 10.0,
            'read_timeout'    => 2,
        ],
        'packet'  => bean('rpcClientPacket')
    ],
    'order.pool'  => [
        'class'  => \Six\Rpc\Client\Pool::class,
        'client' => bean('order'),
        'minActive'   => 50,
        'maxActive'   => 500,
        'maxWait'     => 500,
        'maxWaitTime' => 10,
        'maxIdleTime' => 40,
        'time'=>5
    ],
    'notify'   => [
        'class'   => \Swoft\Rpc\Client\Client::class,
        'serviceName'    => 'notify',
        'version'    => '1.0',
        'setting' => [
            'timeout'         => 0.5,
            'connect_timeout' => 1.0,
            'write_timeout'   => 10.0,
            'read_timeout'    => 2,
        ],
        'packet'  => bean('rpcClientPacket'),
        'provider'=> bean(\App\Test\RpcProvider::class) //服务地址提供类
    ],
    'notify.pool'  => [
        'class'  => \Swoft\Rpc\Client\Pool::class,
        'client' => bean('notify'),
        'minActive'   => 50,
        'maxActive'   => 500,
        'maxWait'     => 500,
        'maxWaitTime' => 10,
        'maxIdleTime' => 40,
        'time'=>5
    ],

    'message'       => [
        'class'   => \App\Rpc\Client\Client::class,
        'serviceName'    => 'message-system',
        'setting' => [
            'timeout'         => 2,
            'connect_timeout' => 3.0,
            'write_timeout'   => 10.0,
            'read_timeout'    => 5,
        ],
        'packet'  => bean('rpcClientPacket')
    ],
    'message.pool'  => [
        'class'  =>\Six\Rpc\Client\Pool::class,
        'client' => bean('message'),
        'minActive'   => 200,
        'maxActive'   => 50000,
        'maxWait'     => 500,
        'maxIdleTime' => 40,
        'timeout'=>5
    ],
    'rpcServer'  => [
        'class' => ServiceServer::class,
        'port'     => 9512,
    ],
    'payAccount'       => [
        'class'   => \App\Rpc\Client\Client::class,
        'serviceName'    => 'payAccount',
        'setting' => [
            'timeout'         => 2.0,
            'connect_timeout' => 3.0,
            'write_timeout'   => 10.0,
            'read_timeout'    =>2.0,
        ],
        'packet'  => bean('rpcClientPacket')
    ],
    'payAccount.pool'  => [
        'class'  =>\Six\Rpc\Client\Pool::class,
        'client' => bean('payAccount'),
        'minActive'   => 200,
        'maxActive'   => 3000,
        'maxWait'     => 500,
        'maxIdleTime' => 40,
        'timeout'=>5
    ],
    'wsServer'   => [
        'class'   => WebSocketServer::class,
        'on'      => [
            // Enable http handle
            SwooleEvent::REQUEST => bean(RequestListener::class),
        ],
        'debug' => env('SWOFT_DEBUG', 0),
        /* @see WebSocketServer::$setting */
        'setting' => [
            'log_file' => alias('@runtime/swoole.log'),
        ],
    ],
    'consulProvider'=>[
        'class'=>\App\Componentes\Consul\ConsulProvider::class
    ],
    'provider'=>[
        'class'=>\App\Componentes\RpcProvider::class
    ]
];

