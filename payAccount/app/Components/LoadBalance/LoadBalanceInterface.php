<?php
/**
 * Created by PhpStorm.
 * User: Sixstar-Peter
 * Date: 2019/6/1
 * Time: 22:28
 */

namespace App\Components\LoadBalance;


interface LoadBalanceInterface
{
     public  static  function select(array $serviceList):array;
}