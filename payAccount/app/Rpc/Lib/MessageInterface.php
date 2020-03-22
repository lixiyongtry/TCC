<?php declare(strict_types=1);


namespace App\Rpc\Lib;

/**
 * Class UserInterface
 *
 * @since 2.0
 */
interface MessageInterface
{
    /**
     * 预发送消息
     * @return array
     */
    public function prepareMsg($prepareMsgData): array;
    /**
     * 确认并且投递参数
     * @return array
     */
    public function confirmMsgToSend($msg_id,$flag): array;

    /**
     * 消息消费成功
     * @return array
     */
    public function ackMsg($msg_id): array;

    /**
     * 消息状态确认
     * @return array
     */
    public function SelectMsgTime($msgType): array;




}