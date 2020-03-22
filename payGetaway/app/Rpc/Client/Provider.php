<?php
/**
 * Created by PhpStorm.
 * User: Sixstar-Peter
 * Date: 2019/5/25
 * Time: 22:48
 */

namespace App\Rpc\Client;

use App\Components\RoundLoadBalance;
use Six\Rpc\Client\Contract\ProviderInterface;
use Swoft\Bean\BeanFactory;
/**
 * Class Provider
 * @package App\Rpc\Client
 */
class Provider implements ProviderInterface
{
    protected  $serviceName;
    public  function __construct($name)
     {
          $this->serviceName=$name;
     }

    public  function  getList(): array
     {

         $config = BeanFactory::getBean('config');
         $config=$config->get('provider.consul');
         $provider=bean('consulProvider');
         $addr=$provider->getServiceList($this->serviceName,$config);
         $arr=array_values($addr);
         $arr=RoundLoadBalance::select($arr);
         //从consul当中获取,当调用失败，记录当前是哪个地址调用失败了
         return [$arr['address']];
     }
}