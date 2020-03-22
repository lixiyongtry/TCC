<?php declare(strict_types=1);


namespace App\Http\Controller;

use App\Components\Snowflake;
use App\Rpc\Lib\MessageInterface;
use App\Rpc\Lib\NotifyInterface;
use App\Rpc\Lib\OrderInterface;
use App\Rpc\Lib\PayAccountInterface;
use App\Rpc\Lib\UserInterface;
use Exception;
use function foo\func;
use Rhumsaa\Uuid\Uuid;
use Six\Rpc\Client\Annotation\Mapping\Reference;
use Six\Tcc\Annotation\Mapping\Compensable;
use Swoft\Http\Server\Annotation\Mapping\Controller;
use Swoft\Http\Server\Annotation\Mapping\RequestMapping;

/**
 * Class RpcController
 *
 * @since 2.0
 *
 * @Controller()
 */
class RpcController
{
    /**
     * @Reference(pool="order.pool",fallback="OrderFallback",type="tcc")
     *
     * @var OrderInterface
     */
    private $orderService;

    /**
     * @Reference(pool="payAccount.pool",fallback="PayAccountFallback",type="tcc")
     *
     * @var PayAccountInterface
     */
    private $payAccountService;


    /**
     * @Reference(pool="order.pool",fallback="OrderFallback",type="tcc")
     *
     * @var OrderInterface
     */
    private $orderService1;
    /**
     * @Reference(pool="payAccount.pool",fallback="PayAccountFallback",type="tcc")
     *
     * @var PayAccountInterface
     */
    private $payAccountService1;

    /**
     * @Reference(pool="message.pool",fallback="MessageFallback")
     *
     * @var MessageInterface
     */
    private $messageService;

    /**
     * @\Swoft\Rpc\Client\Annotation\Mapping\Reference(pool="notify.pool")
     *
     * @var NotifyInterface
     */
    private $notifyService;

    /**
     * @RequestMapping("order")
     *
     * @return array
     */
    public function order()
    {

        //预发送消息（消息状态子系统）
        $msg_id=session_create_id(md5(uniqid()));
        $data=[
            'msg_id'=>$msg_id,
            'version'=>1,
            'create_time'=>time(),
            'message_body'=>['order_id'=>12133,'shop_id'=>2],
            'notify_url'=>'http://127.0.0.1:9804/notify/index',//通知地址
            'notify_rule'=>[1=>5,2=>10,3=>15],//单位为秒
            'notify_retries_number'=>0, //重试次数，
            'default_delay_time'=>1,//毫秒为单位
            'status'=>1, //消息状态
        ];
        //rpc调用服务集群上的某个服务（调用形式是就像调用本地代码一样调用远程代码）
        var_dump($this->notifyService->publish($data));
        return ['1'];
        $prepareMsgData=[
            'msg_id'=>$msg_id,
            'version'=>1,
            'create_time'=>time(),
            'message_body'=>['order_id'=>12133,'shop_id'=>2],
            'consumer_queue'=>'order', //消费队列（消费者）
            'message_retries_number'=>0, //重试次数，
            'status'=>1, //消息状态
         ];

        //预存储消息
        $result = $this->messageService->prepareMsg($prepareMsgData);
        $data=[
            'order_id'=>1,
            'msg_id'=>$msg_id
        ];
        //调用订单服务更新状态
        if ($result['status'] == 1) {  //消息恢复子系统（查询未确认消息）          确认并且投递
               $this->orderService->update($data)['status']==1 && $this->messageService->confirmMsgToSend($msg_id,1);//更新订单
        }
        //确认并且投递消息
        return [$result];
    }

    /**
     * @RequestMapping("success")
     * @Compensable(
     *     master={"services"=OrderInterface::class,"tryMethod"="creditOrderTcc","confirmMethod"="confirmCreditOrderTcc","cancelMethod"="cancelCreditOrderTcc"},
     *     slave ={
     *            {"services"=PayAccountInterface::class,"tryMethod"="creditAccountTcc","confirmMethod"="confirmCreditAccountTcc","cancelMethod"="cancelCreditAccountTcc"}
     *          }
     * )
     *
     * @return array
     */
    public function orderSuccess()
    {
        $context='';
        $this->orderService->creditOrderTcc($context); //主服务触发TCC型调用,从服务只需要定义不需要触发
        //var_dump($this->orderService1->creditOrderTcc($context));
        //$this->payAccountService->creditAccountTcc($context); //商户余额增加
    }


    /**
     * @RequestMapping("error")
     * @Compensable(
     *     master={"services"=OrderInterface::class,"tryMethod"="creditOrderTcc","confirmMethod"="confirmCreditOrderTcc","cancelMethod"="cancelCreditOrderTcc"},
     *     slave ={
     *            {"services"=PayAccountInterface::class,"tryMethod"="creditAccountTcc","confirmMethod"="confirmCreditAccountTcc","cancelMethod"="cancelCreditAccountTcc"},
     *
     *          }
     * )
     * @return array
     */
    public function orderError()
    {
        $context='';
        $this->orderService1->creditOrderTcc($context); //主服务触发TCC型调用,从服务只需要定义不需要触发
        //var_dump($this->orderService1->creditOrderTcc($context));
        //$this->payAccountService->creditAccountTcc($context); //商户余额增加
    }
    /**
     * @RequestMapping("returnBool")
     *
     * @return array
     */
    public function returnBool(): array
    {
        $result = $this->userService->delete(12);

        if (is_bool($result)) {
            return ['bool'];
        }

        return ['notBool'];
    }

    /**
     * @RequestMapping()
     *
     * @return array
     */
    public function bigString(): array
    {
        $this->userService->getBigContent();

        return ['string'];
    }

    /**
     * @RequestMapping()
     *
     * @return array
     *
     * @throws Exception
     */
    public function exception(): array
    {
        $this->userService->exception();

        return ['exception'];
    }
}