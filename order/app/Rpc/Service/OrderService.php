<?php declare(strict_types=1);


namespace App\Rpc\Service;


use App\Rpc\Lib\OrderInterface;
use App\Rpc\Lib\UserInterface;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Co;
use Swoft\Redis\Pool;
use Swoft\Rpc\Server\Annotation\Mapping\Service;

/**
 * Class UserService
 *
 * @since 2.0
 *
 * @Service()
 */
class OrderService implements OrderInterface
{
    /**
     * @Inject("redis.pool")
     * @var Pool
     */
    private $redis;

    /**
     * @param int   $id
     * @param mixed $type
     * @param int   $count
     *
     * @return array
     */
    public function update($data): array
    {
        //业务执行成功
        //调用mysql更新信息（业务逻辑）
        //确认某个任务执行成功
        if($this->redis->hset("order_message_job",(string)$data['msg_id'],(string)1)){
            return ['status'=>1,'result' =>'订单状态更新成功'];
        }
        return ['status'=>1,'result' =>'订单状态更新失败'];
    }
    /**
     * 查询某个任务的状态
     * @param $msg_id
     */
    public  function  confirmStatus($msg_id):array{
        //确认某个任务执行成功
        if(!empty($this->redis->hget("order_message_job",(string)$msg_id))){
            $data=['status'=>1,'result' =>'任务执行成功'];
        }else{
            $data=['status'=>0,'result' =>'任务执行失败'];
        }
        return $data;
    }

    public  function  creditOrderTcc($context):array
    {
        var_dump("一阶段try成功");
        return ['status' => 1, 'result' => '一阶段try成功'];
    }

    /**
     * 确认并且投递参数
     * @return array
     */
    public function confirmCreditOrderTcc($context): array
    {
        var_dump("二阶段confirm成功");
        return ['status' => 1, 'result' => '二阶段confirm成功'];
    }
    public function cancelCreditOrderTcc($context): array
    {
        var_dump("二阶段cancel成功");
        return ['status' => 1, 'result' => '二阶段cancel成功'];
    }

}