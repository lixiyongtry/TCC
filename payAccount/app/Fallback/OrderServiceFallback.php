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
use Six\Rpc\Client\Annotation\Mapping\Fallback;


/**
 * Fallback demo
 *
 * @Fallback(name="OrderFallback",version="1.0")
 */

class OrderServiceFallback implements OrderInterface
{
    public function update($data): array
    {
        return ['status'=>0,'result' =>'订单状态更新降级啦'];
    }
    public  function  confirmStatus($msg_id): array
    {
        return ['status'=>0,'result' =>'查询状态降级啦'];
    }
}