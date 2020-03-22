<?php declare(strict_types=1);


namespace App\Http\Controller;

use App\Rpc\Lib\OrderInterface;
use App\Rpc\Lib\UserInterface;
use Exception;
use Swoft\Http\Server\Annotation\Mapping\Controller;
use Swoft\Http\Server\Annotation\Mapping\RequestMapping;
use Six\Rpc\Client\Annotation\Mapping\Reference;

/**
 * Class RpcController
 *
 * @since 2.0
 *
 * @Controller("rpc")
 */
class RpcController
{
    /**
     * 在程序初始化时候定义好服务降级处理类
     * @Reference(pool="pay.pool",fallback="payFallback")
     * @var OrderInterface
     */
    private $payService;
    /**
     * @RequestMapping("pay")
     *
     * @return array
     */
    public function pay(): array
    {
        $result = $this->payService->pay();
        return [$result];
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