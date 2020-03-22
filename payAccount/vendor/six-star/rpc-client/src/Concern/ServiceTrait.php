<?php declare(strict_types=1);


namespace Six\Rpc\Client\Concern;

use function Couchbase\basicEncoderV1;
use ReflectionException;
use SebastianBergmann\CodeCoverage\Report\PHP;
use Six\Rpc\Client\CircuitBreak;
use Six\Rpc\Client\Connection;
use Six\Rpc\Client\ReferenceRegister;
use Six\Rpc\Client\Route;
use Swoft\Bean\BeanFactory;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Connection\Pool\Exception\ConnectionPoolException;
use Swoft\Log\Debug;
use Swoft\Redis\Redis;
use Swoft\Rpc\Client\Exception\RpcClientException;
use Swoft\Rpc\Protocol;
use Swoft\Stdlib\Helper\JsonHelper;
use Swoole\Exception;

/**
 * Class ServiceTrait
 *
 * @since 2.0
 */
trait ServiceTrait
{
    /**
     * @param string $interfaceClass
     * @param string $methodName
     * @param array $params
     *
     * @return mixed
     * @throws ReflectionException
     * @throws ContainerException
     * @throws ConnectionPoolException
     * @throws RpcClientException
     */
    protected function __proxyCall(string $interfaceClass, string $methodName, array $params)
    {
        $poolName = ReferenceRegister::getPool(__CLASS__);
        $version = ReferenceRegister::getVersion(__CLASS__);
        //获取降级类名称
        $fallback = ReferenceRegister::getFallback(__CLASS__);
        $fallbackName = BeanFactory::getBean(Route::match($fallback, $version, $methodName));
        $circuit = bean('circuit');
        try {
            /* @var Pool $pool */
            $pool = BeanFactory::getBean($poolName);
            /* @var Connection $connection */
            $connection = $pool->getConnection();
            $address = $connection->getAddress();
            $connection->setRelease(true);

            //获取服务状态
            $state = $circuit->getState($address['host'] . ":" . $address['port']);

            //如果熔断开启,直接降级
            if ($state == CircuitBreak::StateOpen) throw  new RpcClientException("Rpc CircuitBreak:" . $fallbackName->$methodName(...$params)["result"]);

            //半开状态,是允许访问后台服务的
            if ($state == CircuitBreak::StateHalfOpen) {
                //满足一定条件之后才允许调用
                if (mt_rand(0, 100) % 2 == 0) {
                    $result = $this->getResult($connection, $version, $interfaceClass, $methodName, $params, $address);
                    //记录成功的次数,大于设定的成功次数的值,熔断就会自动切换成关闭状态
                    $score = $circuit->add($address);
                    if ($score >= 0) Redis::zRem(CircuitBreak::FAILKEY, $address['host'] . ":" . $address['port']);
                    return $result;
                }
                throw  new RpcClientException("Rpc CircuitBreak:" . $fallbackName->$methodName(...$params)["status"]);
            }

            //关闭状态直接调用
            return $this->getResult($connection, $version, $interfaceClass, $methodName, $params, $address);

        } catch (\Exception $e) {
            //如何区分服务,正则匹配ip+port
            $message = $e->getMessage();
            var_dump($message);
            //用于重置连接，因为意外的bug导致错误无法得到信息，或者超时，但是连接正常建立
            if(stristr($message,"Rpc call") || stristr($message,"Rpc CircuitBreak") ){
                $connection->setRelease(true);
                $connection->release();
            }

            //第一种情况是调用失败                    第二种创建连接失败                                第三种连接池里的连接意外断开了
            if (stristr($message, "Rpc call") || stristr($message, "Create connection error") || stristr($message,"Connect failed host")) {
                preg_match("/host=(\d+.\d+.\d+.\d+)\sport=(\d+)/", $message, $mach);
                $address = $mach[1] . ":" . $mach[2];
                $state = $circuit->getState($address);

                //当前状态是关闭状态
                if (CircuitBreak::StateClose == $state) {
                    //记录当前的ip+port所对应方服务的失败次数,失败次数大于允许熔断次数则开启熔断器
                    $score = $circuit->add($address);
                    if ($score >= CircuitBreak::FAILCOUNT) {
                        $circuit->OpenBreaker($address);//开启熔断器,记录半开启延迟时间
                        echo "打开熔断器" . PHP_EOL;
                    }
                    throw  new RpcClientException("Rpc CircuitBreak:" .$fallbackName->$methodName(...$params)["result"]."--正常熔断");
                }

                //当前状态是半开状态只要出现异常,就熔断
                if (CircuitBreak::StateHalfOpen == $state) {
                    //次数重置,重置成熔断次数
                    $circuit->add($address,CircuitBreak::FAILCOUNT);
                    //重新打开熔断器
                    $circuit->OpenBreaker($address);//开启熔断器,记录了延迟时间
                    echo "半开状态重置" . PHP_EOL;
                    throw  new RpcClientException("Rpc CircuitBreak:" . $fallbackName->$methodName(...$params)["result"]."---半开熔断");
                }

                //如果当前熔断是开启状态并且时连接失败的异常
                if (CircuitBreak::StateOpen == $state && stristr($message, "Create connection error")) {
                     throw  new RpcClientException("Rpc CircuitBreak:" . $fallbackName->$methodName(...$params)["result"]."---连接熔断");
                }
            }



            throw  new Exception($e->getMessage()."正常熔断");

        }


    }

    public function getResult($connection, $version, $interfaceClass, $methodName, $params, $address)
    {

        $packet = $connection->getPacket();
        // Ext data
        $ext = $connection->getClient()->getExtender()->getExt();
        $protocol = Protocol::new($version, $interfaceClass, $methodName, $params, $ext);
        $data = $packet->encode($protocol);
        $message = sprintf('Rpc call failed. host=%s port=%d interface=%s method=%s', $address['host'], $address['port'], $interfaceClass, $methodName);

        $result = $this->sendAndRecv($connection, $data, $message);

        $connection->release(); //连接放入到连接池

        $response = $packet->decodeResponse($result);

        if ($response->getError() !== null) {
            $code = $response->getError()->getCode();
            $message = $response->getError()->getMessage();
            $errorData = $response->getError()->getData();
            throw new RpcClientException(
                sprintf('Rpc call error! host=%s port=%d code=%d message=%s data=%s', $address['host'], $address['port'], $code, $message, JsonHelper::encode($errorData))
            );
        }
        return $response->getResult();
    }

    /**
     * @param Connection $connection
     * @param string $data
     * @param string $message
     * @param bool $reconnect
     *
     * @return string
     * @throws RpcClientException
     * @throws ReflectionException
     * @throws ContainerException
     */
    private function sendAndRecv(Connection $connection, string $data, string $message, bool $reconnect = false): string
    {
        //Reconnect
        if ($reconnect) {
            $connection->reconnect();
        }

        if (!$connection->send($data)) {
            if ($reconnect) {
                throw new RpcClientException($message);
            }
            //重发一次
            return $this->sendAndRecv($connection, $data, $message, true);
        }

        $result = $connection->recv();
        if ($result === false || empty($result)) {
            if ($reconnect) {
                throw new RpcClientException($message);
            }
            return $this->sendAndRecv($connection, $data, $message, true);
        }

        return $result;
    }
}