<?php declare(strict_types=1);


namespace Six\Tcc\Annotation\Parser;

use PhpDocReader\AnnotationException;
use ReflectionException;
use Six\Rpc\Client\Route;
use Six\Tcc\Annotation\Mapping\Compensable;
use Six\Tcc\RouteRegister;
use Swoft\Annotation\Annotation\Mapping\AnnotationParser;
use Swoft\Annotation\Annotation\Parser\Parser;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Proxy\Exception\ProxyException;
use Six\Rpc\Client\Annotation\Mapping\Fallback;
use Swoft\Rpc\Client\Exception\RpcClientException;
/**
 * @since 2.0
 *
 * @AnnotationParser(Compensable::class)
 */
class CompensableParser extends Parser
{
    /**
     * @param int       $type
     * @param Reference $annotationObject
     *
     * @return array
     * @throws RpcClientException
     * @throws AnnotationException
     * @throws ReflectionException
     * @throws ProxyException
     */
    public function parse(int $type, $annotationObject): array
    {

        $service=$annotationObject->getTccService();
        RouteRegister::register($service);
         //返回当前类名注册到框架的bean容器当中
         return  [$this->className,$this->className,Bean::SINGLETON,''];
    }
}