<?php

namespace App\Componentes\Consul;
use App\Application;
use Co\Client;
use function Swlib\Http\_caseless_remove;
use Swlib\Saber;
use Swlib\SaberGM;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Config\Config;
use Yurun\Util\HttpRequest;

/**
 * Consul provider
 * @Bean(name="ConsulProvider")
 */
class ConsulProvider {

    const REGISTER_PATH = '/v1/agent/service/register';
    const DISCOVERY_PATH = '/v1/health/service/';
    const KV_PATH='/v1/kv';
    private $address = "http://127.0.0.1";
    private $port = 8500;
    private $registerId = '';
    private $registerName = 'user';
    private $registerTags = [];

    private $registerEnableTagOverride = false;

    private $registerAddress = 'http://127.0.0.1';

    private $registerPort = 88;

    private $registerCheckId = '';


    private $registerCheckName = 'user';

    private $registerCheckTcp = '127.0.0.1:8099';

    private $registerCheckInterval = 10;

    private $registerCheckTimeout = 1;

    private $discoveryDc = "";
    private $discoveryNear = "";

    private $discoveryTag = "";

    private $discoveryPassing = true;

    public function getServiceList(string $serviceName, $config)
    {
        $query = [
            'passing' =>$config['discovery']['passing'],
            'dc'      =>$config['discovery']['dc']
        ];
        if (!empty($config['discovery']['tag'])) {
            $query['tag'] =$config['discovery']['tag'];
        }
        $queryStr    = http_build_query($query);
        $path        = sprintf('%s%s', self::DISCOVERY_PATH, $serviceName);
        $result=$this->Curl_request('http://'.$config['address'].':'.$config['port'].$path."?".$queryStr,'GET');
        $services   = json_decode($result, true);
        $address=[];
        foreach ($services as $key=>$v){
            foreach ($v['Checks'] as $k=>$c){
                //判断是否是活跃的,并且名称是想要查询的服务
                if($c['ServiceName']==$serviceName && $c['Status']=='passing'){
                    $address[$key]['address']=$v['Service']['Address'].':'.$v['Service']['Port'];
                    $address[$key]['Weight']=$v['Service']['Weights']['Passing'];
                }
            }
        }
        return $address;

    }

    /**
     * register service
     *
     * @param array ...$params
     *
     * @return bool
     */
    public function registerService(...$params)
    {

         //$request=new HttpRequest();
         //$request->put('http://'.$params[0]['address'].':'.$params[0]['port'].self::REGISTER_PATH,json_encode($params[0]['register']));
         $result=$this->Curl_request('http://'.$params[0]['address'].':'.$params[0]['port'].self::REGISTER_PATH,'PUT',json_encode($params[0]['register']));
         output()->writeln(sprintf('<success>RPC service register success by consul ! tcp=%s:%d</success>', $params[0]['address'],$params[0]['port']));

    }


    /**
     * @param string $serviceName
     * @return string
     */
    private function getDiscoveryUrl(string $serviceName): string
    {
        $query = [
            'passing' => $this->discoveryPassing,
            'dc'      => $this->discoveryDc,
            'near'    => $this->discoveryNear,
        ];

        if (!empty($this->discoveryTag)) {
            $query['tag'] = $this->discoveryTag;
        }

        $queryStr    = http_build_query($query);
        $path        = sprintf('%s%s', self::DISCOVERY_PATH, $serviceName);
        return      sprintf('%s:%d%s?%s', $this->address, $this->port, $path, $queryStr);
    }

    public  function Curl_request($url, $method = 'POST', $data = [])
    {
        $method = strtoupper($method);
        //初始化
        $ch = curl_init();
        //设置请求地址
        curl_setopt($ch, CURLOPT_URL, $url);
        // 检查ssl证书
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // 从检查本地证书检查是否ssl加密
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, "Content-type:application/json;charset=utf-8", "Accept:application/json");
        //设置请求数据
        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }

    public function  putKV($data,$config){

        $result=$this->Curl_request('http://'.$config[0]['address'].':'.$config[0]['port'].self::KV_PATH,'PUT',$data);
        var_dump($result);
    }

}
