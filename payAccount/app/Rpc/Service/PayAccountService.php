<?php declare(strict_types=1);


namespace App\Rpc\Service;

use App\Rpc\Lib\MessageInterface;
use App\Rpc\Lib\OrderInterface;
use App\Rpc\Lib\PayAccountInterface;
use App\Rpc\Lib\UserInterface;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Co;
use Swoft\Redis\Pool;
use Swoft\Rpc\Server\Annotation\Mapping\Service;
use Swoole\Coroutine;

/**
 * Class UserService
 * @since 2.0
 * @Service()
 */
class PayAccountService implements PayAccountInterface
{
    /**
     * @Inject("redis.pool")
     * @var Pool
     */
    private $connectionRedis;

    /**
     * @Inject("rabbit.pool")
     * @var \Six\Rabbit\Pool
     */
    private $rabbit;


    public  function  creditAccountTcc($context):array
    {
        var_dump("一阶段try成功");
        return ['status' => 1, 'result' => '一阶段try成功'];
    }


    /**
     * 确认并且投递参数
     * @return array
     */
    public function confirmCreditAccountTcc($context): array
    {
        var_dump("二阶段confirm成功");
        return ['status' => 1, 'result' => '二阶段confirm成功'];
    }
    public function cancelCreditAccountTcc($context): array
    {
        var_dump("二阶段cancel成功");
        return ['status' => 1, 'result' => '二阶段cancel成功'];
    }




}