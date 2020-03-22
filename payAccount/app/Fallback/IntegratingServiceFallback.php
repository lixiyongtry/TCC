<?php declare(strict_types=1);


namespace App\Fallback;


use App\Rpc\Lib\IntegratingInterface;
use Six\Rpc\Client\Annotation\Mapping\Fallback;

/**
 * Class UserService
 *
 * @since 2.0
 *
 * @Fallback(name="IntegratingFallback",version="1.0")
 */
class IntegratingServiceFallback implements IntegratingInterface
{
    /**
     * @param int   $id
     * @param mixed $type
     * @param int   $count
     *
     * @return array
     */
    public function add(): array
    {
        return ['status'=>0,'result' =>"积分降级成功"];
    }

}