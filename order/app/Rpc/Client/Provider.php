<?php
/**
 * Created by PhpStorm.
 * User: Sixstar-Peter
 * Date: 2019/5/25
 * Time: 22:48
 */

namespace App\Rpc\Client;


use App\Components\LoadBalance\RandLoadBalance;
use Six\Rpc\Client\Contract\ProviderInterface;

class Provider implements ProviderInterface
{
    protected $serviceName;

    public function __construct($serviceName)
    {
        $this->serviceName = $serviceName;
    }

    public function getList(): array
    {
        $config = bean('config')->get('provider.consul');
        $address = bean('consulProvider')->getServerList($this->serviceName, $config);
        //负载均衡(加权随机)
        $address = RandLoadBalance::select(array_values($address))['address'];
        //根据服务名称consul当中获取动态地址
        return ['127.0.0.1:9512','127.0.0.1:9512'];
    }
}