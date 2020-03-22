<?php
/**
 * This file is part of Swoft.
 *
 * @link https://swoft.org
 * @document https://doc.swoft.org
 * @contact group@swoft.org
 * @license https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

namespace App\Fallback;
use App\Rpc\Lib\OrderInterface;
use App\Rpc\Lib\PayAccountInterface;
use Six\Rpc\Client\Annotation\Mapping\Fallback;


/**
 * Fallback demo
 *
 * @Fallback(name="PayAccountFallback",version="1.0")
 */

class PayAccountServiceFallback implements PayAccountInterface
{

    public function creditAccountTcc($context): array
    {
        // TODO: Implement creditOrderTcc() method.
        return ['status'=>0,'result' =>'查询状态降级啦'];
    }

    public function cancelCreditAccountTcc($context): array
    {
        // TODO: Implement creditOrderTcc() method.
        return ['status'=>0,'result' =>'查询状态降级啦'];
    }

    public function confirmCreditAccountTcc($context): array
    {
        // TODO: Implement creditOrderTcc() method.
        return ['status'=>0,'result' =>'查询状态降级啦'];
    }

}