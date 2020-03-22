<?php declare(strict_types=1);


namespace Six\Rpc\Client;


use function count;
use function explode;
use ReflectionException;
use Six\Rpc\Client\Contract\ConnectionInterface;
use Six\Rpc\Client\Contract\ProviderInterface;
use function sprintf;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Concern\PrototypeTrait;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Connection\Pool\AbstractConnection;
use Swoft\Log\Debug;
use Swoft\Rpc\Client\Exception\RpcClientException;
use Swoft\Rpc\Contract\PacketInterface;
use Swoft\Stdlib\Helper\JsonHelper;
/**
 * Class Connection
 *
 * @since 2.0
 *
 * @Bean(scope=Bean::PROTOTYPE)
 */
class Connection extends AbstractConnection implements ConnectionInterface
{
    use PrototypeTrait;

    protected $connection;

    protected $client;

    protected $host;

    protected $port;

    /**
     * @param \Swoft\Rpc\Client\Client $client
     * @param Pool                     $pool
     *
     * @return Connection
     * @throws ReflectionException
     * @throws ContainerException
     */
    public static function new(Client $client, Pool $pool): Connection
    {
        $instance = self::__instance();
        $instance->client = $client;
        $instance->pool   = $pool;
        $instance->lastTime = time();
        return $instance;
    }

    /**
     * @throws RpcClientException
     */
    public function create(): void
    {
        $connection = new \Co\Client(SWOOLE_SOCK_TCP);
        [$host, $port] = $this->getHostPort();
        $setting = $this->client->getSetting();
        //赋值属性用于区分服务
        $this->host=$host;
        $this->port=$port;

        if (!empty($setting)) {
            $connection->set($setting);
        }
        if (!$connection->connect($host, (int)$port)) {
            throw new RpcClientException(
                sprintf('Connect failed host=%s port=%d', $host, $port)
            );

        }
        $this->connection = $connection;
    }
    /*
     * 获取当前连接的地址
     */
    public function  getAddress(){
        return ['host'=>$this->host,'port'=>$this->port];
    }
    /**
     * Close connection
     */
    public function close(): void
    {
        $this->connection->close();
    }

    /**
     * @return bool
     * @throws RpcClientException
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function reconnect(): bool
    {
        $this->create();
        Debug::log('Rpc client reconnect success!');
        return true;
    }

    /**
     * @return PacketInterface
     * @throws RpcClientException
     */
    public function getPacket(): PacketInterface
    {
        return $this->client->getPacket();
    }

    /**
     * @return \Swoft\Rpc\Client\Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @param string $data
     *
     * @return bool
     */
    public function send(string $data): bool
    {
        return (bool)$this->connection->send($data);
    }

    /**
     * @return string|bool
     */
    public function recv()
    {
        return $this->connection->recv((float)-1);
    }

    /**
     * @return array
     * @throws RpcClientException
     */
    public function getHostPort(): array
    {
        $provider = $this->client->getProvider();

        if (empty($provider) || !$provider instanceof ProviderInterface || empty(env('AUTOLOAD_REGISTER'))) {

            return [$this->client->getHost(), $this->client->getPort()];
        }
        $list = $provider->getList();
        if (!is_array($list)) {
            throw new RpcClientException(
                sprintf('Provider(%s) return format is error!', JsonHelper::encode($list))
            );
        }
        $randKey  = array_rand($list, 1);
        $hostPort = explode(':', $list[$randKey]);

        if (count($hostPort) < 2) {
            throw new RpcClientException(
                sprintf('Provider(%s) return format is error!', JsonHelper::encode($hostPort))
            );
        }

        [$host, $port] = $hostPort;

        return [$host, $port];
    }
}