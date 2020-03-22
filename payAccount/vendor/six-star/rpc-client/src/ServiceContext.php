<?php declare(strict_types=1);


namespace Six\Rpc\Client;


use ReflectionException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Concern\PrototypeTrait;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Context\AbstractContext;

/**
 * Class ServiceContext
 * @since 2.0
 * @Bean(scope=Bean::PROTOTYPE)
 */
class ServiceContext extends AbstractContext
{
    use PrototypeTrait;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return ServiceContext
     * @throws ReflectionException
     * @throws ContainerException
     */
    public static function new(): self
    {
        $instance = self::__instance();
        return $instance;
    }

    /**
     * Clear
     */
    public function clear(): void
    {
        $this->data    = [];
        $this->request = $this->response = null;
    }
}