<?php
/**
 * Created by PhpStorm.
 * User: Sixstar-Peter
 * Date: 2019/6/1
 * Time: 22:30
 */

namespace App\Components\LoadBalance;


class RandLoadBalance implements LoadBalanceInterface
{
    public static function select(array $serviceList): array
    {
        $sum = 0; //总的权重值
        $weightsList = [];
        foreach ($serviceList as $k => $v) {
            $sum += $v['weight'];
            $weightsList[$k]=$sum;
        }
        $rand=mt_rand(0,$sum);
        //var_dump($weightsList,'随机数'.$rand);
        foreach ($weightsList as $k=>$v){
                if($rand<=$v){
                    return $serviceList[$k];
                }
        }

    }
}