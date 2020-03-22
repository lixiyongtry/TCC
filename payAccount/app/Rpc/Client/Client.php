<?php
/**
 * Created by PhpStorm.
 * User: Sixstar-Peter
 * Date: 2019/5/25
 * Time: 22:45
 */

namespace App\Rpc\Client;



use Six\Rpc\Client\Contract\ProviderInterface;

class Client extends  \Six\Rpc\Client\Client
{
    protected  $serviceName; //服务名称
    public function getProvider(): ?ProviderInterface
    {
        //不能区分当前调用的服务是哪个
         return $this->provider=new Provider($this->getServiceName());
    }
    /*
     * 获取服务名称
     */
    public  function  getServiceName(){
        return $this->serviceName;
    }
}