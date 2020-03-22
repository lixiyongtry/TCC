<?php declare(strict_types=1);


namespace App\Rpc\Lib;

/**
 * Class UserInterface
 *
 * @since 2.0
 */
interface PayAccountInterface
{
    /**
     * 尝试提交(第一阶段try)
     * @param $context
     * @return mixed
     */
    public function creditAccountTcc($context):array;

    /**
     * 业务提交执行(第二阶段Confirm)
     * @param $context
     * @return mixed
     */
    public function confirmCreditAccountTcc($context):array;

    /**
     * 回滚(第二阶段cancel)
     * @param $context
     * @return mixed
     */
    public function cancelCreditAccountTcc($context):array;

}