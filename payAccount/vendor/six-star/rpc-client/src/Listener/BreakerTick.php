<?php
/**
 * Created by PhpStorm.
 * User: Sixstar-Peter
 * Date: 2019/5/29
 * Time: 21:59
 */

namespace Six\Rpc\Client\Listener;


use Six\Rpc\Client\CircuitBreak;
use Swoft\Event\Annotation\Mapping\Listener;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\Redis\Redis;
use Swoft\Server\Swoole\SwooleEvent;

/**
 * Class RegisterServer
 * @package App\Listener
 * @Listener(SwooleEvent::START)
 */
class BreakerTick implements EventHandlerInterface
{
    public function handle(EventInterface $event): void
    {

        swoole_timer_tick(2000, function () {
            $service = Redis::zRangeByScore(CircuitBreak::OpenBreaker, "-inf", (string)time());
            //修改
            if (!empty($service)) {
                foreach ($service as $s) {
                   //把失败次数重置成负数,改变成立半开启状态
                    Redis::zAdd(CircuitBreak::FAILKEY,[$s =>-CircuitBreak::SuccessCount]);
                   Redis::zRem(CircuitBreak::OpenBreaker,$s); //延迟时间删除，避免重复处理
                   echo '修改了'.$s."为半开启状态".PHP_EOL;
                }
            }

        });
    }
}