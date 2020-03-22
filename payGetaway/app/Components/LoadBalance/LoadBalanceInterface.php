<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/1
 * Time: 17:04
 */

namespace App\Components;


interface LoadBalanceInterface
{
    public   static function   select( array $service):array;
}