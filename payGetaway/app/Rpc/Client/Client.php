<?php

namespace App\Rpc\Client;
use App\Compontes\Consul\ConsulProvider;
use Six\Rpc\Client\Connection;
use Six\Rpc\Client\Contract\ProviderInterface;
use Six\Rpc\Client\Pool;


/**
 * Class Client
 *
 * @since 2.0
 */
class Client extends \Six\Rpc\Client\Client
{
    protected $serviceName;

    public function getProvider(): ?ProviderInterface
    {
        return $this->provider=new Provider($this->getName());
    }

    public  function  getName(){
         return $this->serviceName;
    }
}