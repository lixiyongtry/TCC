<?php
/**
 * Created by PhpStorm.
 * User: Sixstar-Peter
 * Date: 2019/7/27
 * Time: 22:45
 */

namespace Six\Tcc;

use Swoole\Exception;

class Tcc
{
    /**
     *
     * @param $services
     * @param $interfaceClass
     * @param $tcc_methodName
     * @param $params
     * @param $tid
     * @param $obj
     * @return array
     */
    public static function Tcc($services, $interfaceClass, $tcc_methodName, $params, $tid, $obj)
    {
        try {
            $flag = 0;
            if ($tcc_methodName == 'confirmMethod') {
                $flag = 1;
            }
            //记录当前事务处于哪个阶段
            $data['tcc_method'] = $tcc_methodName;
            $data['status'] = 'normal';
            self::tccStatus($tid,3,$tcc_methodName,$data);

            //整体的并发请求，等待一组协程的结果，发起第二阶段的请求
            $wait = new WaitGroup(count($services['slave']) + 1);
            //sgo(function ()use($wait,$services,$interfaceClass,$params,$obj,$tcc_methodName){
            //主服务发起第一阶段的请求
            $res = $obj->send($services['master']['services'], $interfaceClass, $services['master'][$tcc_methodName], $params);
            $res['interfaceClass'] = $interfaceClass;
            $res['method'] = $tcc_methodName;
            //当结果正常,修改主服务的状态
//            if (!empty($res['status']) || $res['status'] == 1) {
//                $data['services']['tcc_method'] = $tcc_methodName;
//                $data['services']['status'] = 'success';
//                self::tccStatus($tid, 3,json_encode($data));
//            }

            $wait->push($res);
            // });
            //从服务发起第一阶段的请求
            foreach ($services['slave'] as $k => $slave) {
                //sgo(function ()use($wait,$slave,$params,$obj,$tcc_methodName){
                $slaveInterfaceClass = explode("_", $slave['services'])[0];
                $slaveRes = $obj->send($slave['services'], $slaveInterfaceClass, $slave[$tcc_methodName], $params);  //默认情况下从服务没有办法发起请求
                $slaveRes['interfaceClass'] = $slaveInterfaceClass;
                $slaveRes['method'] = $tcc_methodName;
//                //当结果正常,修改从服务的状态
//                if (!empty($slaveRes['status']) || $slaveRes['status'] == 1) {
//                    $data['services']['slave'][$k]['tcc_method'] = $tcc_methodName;
//                    $data['services']['slave'][$k]['status'] = 'success';
//                }
//                self::tccStatus($tid, 3,json_encode($data));
                $wait->push($slaveRes);
                // });
            }
            //等待一阶段调用结果
            $res = $wait->wait();  //阻塞
            foreach ($res as $v) {
                if (empty($v['status']) || $v['status'] == 0) {
                    throw  new \Exception("Tcc error!:" . $tcc_methodName);
                    return;
                }
            }
            //假设当前操作没有任何问题
            $data['tcc_method'] = $tcc_methodName;
            $data['status'] = 'success';
            self::tccStatus($tid, 3,$tcc_methodName,$data); //整体服务的状态
            //只有在当前的方法为try时才提交
            if ($tcc_methodName == 'tryMethod') {
                //第二阶段提交
                if ($flag == 0) {
                    return self::Tcc($services, $interfaceClass, 'confirmMethod', $params, $tid, $obj);
                }
            }
            return $res;
        } catch (\Exception $e) {
            $message = $e->getMessage();
            echo 'Tcc  message:'.$e->getMessage().' line:'.$e->getFile().' file:'.$e->getFile().PHP_EOL;
            //无论是哪个服务抛出异常,回滚所有的服务
            if (stristr($message, "Tcc error!") || stristr($message, "Rpc CircuitBreak")) {
                //结果异常，跟调用异常的标准不同（也是因为swoft框架做了一次重试操作）
                //回滚时记录当前的回滚次数,下面这段仅仅只是结果出现异常的回滚
                //调用出现异常(调用超时,网络出现问题,调用失败)
                //在try阶段的回滚,直接调用cancel回滚
                //在(cancel)阶段的时候出现了异常,会重试有限次数,重复调用cancel,超过最大次数,设置fail状态,抛出异常
                //在(confirm)阶段的时候,会重试有限次数,重复调用confirm,超过最大次数,调用cancel,补偿机制跟try阶段不一样

                if ($tcc_methodName == 'tryMethod') {
                    return self::Tcc($services, $interfaceClass, 'cancelMethod', $params, $tid, $obj);
                } elseif ($tcc_methodName == 'cancelMethod') {
                    if (self::tccStatus($tid, 1, $tcc_methodName)) {
                        return self::Tcc($services, $interfaceClass, 'cancelMethod', $params, $tid, $obj);
                    }
                    return ["回滚异常"];
                } elseif ($tcc_methodName == 'confirmMethod') {
                    if (self::tccStatus($tid,2, $tcc_methodName)) {
                        return self::Tcc($services, $interfaceClass, $tcc_methodName, $params, $tid, $obj);
                    }
                    //意味当前已经有方法提交了，有可能执行了业务了，我们需要额外的补偿
                    $params[0]['cancel_confirm_flag']=1;
                    return self::Tcc($services, $interfaceClass, 'cancelMethod', $params, $tid, $obj);
                }
            }
        }

    }

    public static function tccStatus($tid, $flag = 1, $tcc_method = '', $data = [])
    {
        $redis = new \Co\Redis();
        $redis->connect('127.0.0.1', 6379);
        $originalData = $redis->hget("Tcc", $tid);
        $originalData = json_decode($originalData, true);
        //(回滚处理)修改回滚次数,并且记录当前是哪个阶段出现了异常
        if ($flag == 1) {
            //判断当前事务重试的次数为几次,如果重试次数超过最大次数,则取消重试
            if ($originalData['retried_cancel_count'] >= $originalData['retried_max_count']) {
                $originalData['status'] = 'fail';
                $redis->hSet('Tcc', $tid, json_encode($originalData));
                return false;
            }
            $originalData['retried_cancel_count']++;
            $originalData['tcc_method'] = $tcc_method;
            $originalData['status'] = 'abnormal';
            $originalData['last_update_time']=time();
            $redis->hSet('Tcc', $tid, json_encode($originalData));
            return true;
        }

        //(confirm处理)修改尝试次数,并且记录当前是哪个阶段出现了异常
        if ($flag == 2) {
            //判断当前事务重试的次数为几次,如果重试次数超过最大次数,则取消重试
            if ($originalData['retried_confirm_count'] >=1) {
                $originalData['status'] = 'fail';
                $redis->hSet('Tcc', $tid, json_encode($originalData));
                return false;
            }
            $originalData['retried_confirm_count']++;
            $originalData['tcc_method'] = $tcc_method;
            $originalData['status'] = 'abnormal';
            $originalData['last_update_time']=time();
            $redis->hSet('Tcc', $tid, json_encode($originalData));
            return true;
        }
        //修改当前事务的阶段
        if ($flag == 3) {
            $originalData['tcc_method']=$data['tcc_method'];
            $originalData['status']=$data['status'];
            $originalData['last_update_time']=time();
            $redis->hSet('Tcc', $tid, json_encode($originalData)); //主服务状态
        }

    }
}