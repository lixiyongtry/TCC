<?php
/**
 * This file is part of Swoft.
 *
 * @link https://swoft.org
 * @document https://doc.swoft.org
 * @contact group@swoft.org
 * @license https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

namespace App\Listener;
use Co\Context;
use Swoft\Event\Annotation\Mapping\Listener;
use Swoft\Event\EventHandlerInterface;
use Swoft\Rpc\Client\Exception\RpcClientException;
use Swoft\Server\ServerEvent;
use Swoole\Exception;
use Swoft\Event\EventInterface;


/**
 * Task finish handler
 *
 * @Listener("rpcCall")
 */
class RpcCall implements EventHandlerInterface
{

    public function handle(EventInterface $event):void
    {
        //$addr=$event->getParams();
        //var_dump("调用触发",$addr);
        //判断当前请求是否是一个熔断服务，如果是，那么就触发，并且调用fall back
        // throw new RpcClientException(sprintf("rpc call  host=%s port=%d",$addr[0][0],$addr[0][1]));
    }
}