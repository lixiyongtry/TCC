<?php
/**
 * Created by PhpStorm.
 * User: Sixstar-Peter
 * Date: 2019/5/29
 * Time: 22:27
 */

namespace App\Components\Consul;

class ConsulProvider
{
    //http://118.24.109.254:8500/v1/agent/services  展示所有的服务
    //http://118.24.109.254:8500/v1/catalog/service/pay-php  某个服务的多个服务地址
    //http://118.24.109.254:8500/v1/health/service/pay-php   某个服务的多个服务地址并且查看健康的状态
    const REGISTER_PATH = '/v1/agent/service/register'; //服务注册路径
    const HEALTH_PATH = '/v1/health/service/'; //获取健康服务

    public function registerServer($config)
    { 
        //echo 'http://'.$config['address'].':'.$config['port'].self::REGISTER_PATH,json_encode($config['register']);
        //注册地址底层错误无法使用
        if (env('AUTOLOAD_REGISTER')) {
            //var_dump(json_encode($config['register']));
            $this->curl_request('http://' . $config['address'] . ':' . $config['port'] . self::REGISTER_PATH, "PUT", json_encode($config['register']));
            output()->writeln("<success>Rpc service Register success by consul tcp=" . $config['address'] . ":" . $config['port'] . "</success>");
        }
    }

    /**
     * 获取某个服务的列表
     */
    public function getServerList($serviceName, $config)
    {
        $query=[
            'dc'=>$config['discovery']['dc']
        ];
        if(!empty($config['discovery']['tag'])){
            $query['tag']=$config['discovery']['tag'];
        }
        $queryStr=http_build_query($query);
        //排除不健康的服务,获取健康服务
        $url = 'http://' . $config['address'] . ':' . $config['port'] . self::HEALTH_PATH . $serviceName.'?'.$queryStr;
        //负载机制
        $serviceList = $this->curl_request($url, 'GET');

        $serviceList=json_decode($serviceList, true);
        $address=[];
        foreach ($serviceList as $k=>$v){
            //判断当前的服务是否是活跃的,并且是当前想要去查询服务
            foreach ($v['Checks'] as $c){
                if($c['ServiceName']==$serviceName && $c['Status']=="passing"){
                    $address[$k]['address']=$v['Service']['Address'].":".$v['Service']['Port'];
                    $address[$k]['weight']=$v['Service']['Weights']['Passing'];
                }
            }
        }
        return $address;
    }


    public function curl_request($url, $method = 'POST', $data = [])
    {
        $method = strtoupper($method);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }
}