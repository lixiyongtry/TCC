<?php declare(strict_types=1);


namespace Six\Rabbit;


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
class Connection extends AbstractConnection
{
    use PrototypeTrait;

    public $connection;

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
    public static function new($client, Pool $pool): Connection
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
      $connection = new \PhpAmqpLib\Connection\AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $setting = $this->client->getSetting();
        //赋值属性用于区分服务
        if (!empty($setting)) {
            //$connection->set($setting);
        }
        if (!$connection->isConnected()) {
            throw new \Exception(
                sprintf('Connect failed host=%s port=%d', '127.0.0.1', 5672)
            );
        }
        $this->connection = $connection;
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
     * @return \Swoft\Rpc\Client\Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }
}