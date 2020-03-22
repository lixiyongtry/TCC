<?php
/**
 * Created by PhpStorm.
 * User: Sixstar-Peter
 * Date: 2019/5/29
 * Time: 21:59
 */

namespace App\Listener;


use App\Rpc\Lib\MessageInterface;
use App\Rpc\Lib\OrderInterface;
use Six\Rpc\Client\Annotation\Mapping\Reference;
use Six\Rpc\Client\ServiceContext;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Context\Context;
use Swoft\Event\Annotation\Mapping\Listener;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\Redis\Pool;
use Swoft\Server\ServerEvent;
use Swoft\Server\Swoole\SwooleEvent;

/**
 * Class RegisterServer
 * @package App\Listener
 * @Listener(SwooleEvent::START)
 */

class MessageStatusListener implements EventHandlerInterface
{
    /**
     * @Reference(pool="order.pool",fallback="OrderFallback")
     *
     * @var OrderInterface
     */
    private $orderService;

    /**
     * @Reference(pool="message.pool",fallback="MessageFallback")
     *
     * @var MessageInterface
     */
    private $messageService;

    /**
     * @Inject("redis.pool")
     * @var Pool
     */
    private $connection;

    public function handle(EventInterface $event): void
    {

        //            $time=1; //超时任务
        //            //自动初始化一个Context上下文对象(协程环境下)
        //            $context = ServiceContext::new();
        //            Context::set($context);



    }
}