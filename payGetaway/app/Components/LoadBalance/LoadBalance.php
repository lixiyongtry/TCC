<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/1
 * Time: 17:02
 */

namespace App\Components;


class RoundLoadBalance implements LoadBalanceInterface
{
     public static  function  select( array $service):array{

         if(empty($service)){
             throw  new \Exception("无效的地址列表");
         }
    	$sumList=[];
        $sum=0;
        foreach ($service as $k=>$v){
            $sum+=$v['Weight'];
            $sumList[$k]=$sum;
        }
         $rand=mt_rand(1,$sum);
         foreach ($sumList as $k=>$v){
            if($rand<=$v){
                return $service[$k];
            }
         }
     }
}