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
use Swoft\Event\Annotation\Mapping\Listener;
use Swoft\Event\EventHandlerInterface;
use Swoft\Server\ServerEvent;


/**
 * Task finish handler
 *
 * @Listener(ServerEvent::BEFORE_START)
 */
class TaskFinish implements EventHandlerInterface
{

    public function handle($event):void
    {
       // sgo(function(){
            $provider=bean('consulProvider');
            $config = bean('config');
            $config=$config->get('provider.consul');
            // rpc服务启动时注册服务
            $provider->registerService($config);
       //});
        //var_dump('task finish! ', $event->getParams());
    }
}