<?php declare(strict_types=1);


namespace App\Fallback;


use App\Rpc\Lib\MessageInterface;
use Six\Rpc\Client\Annotation\Mapping\Fallback;



/**
 * Fallback demo
 *
 * @Fallback(name="MessageFallback",version="1.0")
 */
class MessageServiceFallback implements MessageInterface
{
    /**
     * 预发送消息
     * @return array
     */
    public function prepareMsg($prepareMsgData): array
    {
        return ['status'=>1,'result' =>'预发送消息降级'];
    }

    /**
     * 确认并且投递参数
     * @return array
     */
    public function confirmMsgToSend($msg_id,$flag): array
    {

        return ['status'=>1,'result' =>'确认并且投递降级'];

    }

    /**
     * 消息消费成功
     * @return array
     */
    public function ackMsg($msg_id): array
    {
        return ['status'=>1,'result' =>'任务消费降级'];
    }

    /**
     * 消息状态确认
     * @return array
     */
    public function SelectMsgTime($msgType): array
    {
        return ['status'=>1,'result' =>'查询任务状态降级'];
    }

}