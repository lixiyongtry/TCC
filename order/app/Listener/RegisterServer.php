<?php
/**
 * Created by PhpStorm.
 * User: Sixstar-Peter
 * Date: 2019/5/29
 * Time: 21:59
 */

namespace App\Listener;


use Swoft\Event\Annotation\Mapping\Listener;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\Server\ServerEvent;

/**
 * Class RegisterServer
 * @package App\Listener
 * @Listener(ServerEvent::BEFORE_START)
 */
class RegisterServer implements EventHandlerInterface
{
    public function handle(EventInterface $event): void
    {

        $config = bean('config')->get('provider.consul');
        bean('consulProvider')->registerServer($config);
    }
}