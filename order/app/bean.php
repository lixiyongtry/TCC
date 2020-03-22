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
        'port'     => 9501,
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
        'maxActive'   => 50000,
        'maxWait'     => 0,
        'maxWaitTime' => 0,
        'maxIdleTime' => 40,
    ],
    'rpcServer'  => [
        'class' => ServiceServer::class,
        'port'     => 9802,
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
        'class'=>\App\Components\Consul\ConsulProvider::class
    ],
];
