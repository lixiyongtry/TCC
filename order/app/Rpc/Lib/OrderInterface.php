<?php declare(strict_types=1);


namespace App\Rpc\Lib;

/**
 * Class UserInterface
 *
 * @since 2.0
 */
interface OrderInterface
{
    /**
     * @return array
     */
    public function update($data): array;

    public function confirmStatus($msg_id): array;

    /**
     * 尝试提交(第一阶段try)
     * @param $context
     * @return mixed
     */
    public function creditOrderTcc($context):array;

    /**
     * 业务提交执行(第二阶段Confirm)
     * @param $context
     * @return mixed
     */
    public function confirmCreditOrderTcc($context):array;
    /**
     * 回滚(第二阶段cancel)
     * @param $context
     * @return mixed
     */
    public function cancelCreditOrderTcc($context):array;
}