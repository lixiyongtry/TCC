<?php declare(strict_types=1);


namespace Six\Rpc\Client\Annotation\Parser;

use PhpDocReader\AnnotationException;
use PhpDocReader\PhpDocReader;
use ReflectionException;
use ReflectionProperty;
use Six\Rpc\Client\Proxy;
use Six\Rpc\Client\ReferenceRegister;
use Six\Rpc\Client\Route;
use Swoft\Annotation\Annotation\Mapping\AnnotationParser;
use Swoft\Annotation\Annotation\Parser\Parser;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Proxy\Exception\ProxyException;
use Six\Rpc\Client\Annotation\Mapping\Fallback;
use Swoft\Rpc\Client\Exception\RpcClientException;
/**
 * @since 2.0
 *
 * @AnnotationParser(Fallback::class)
 */
class FallbackParser extends Parser
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
            foreach ($this->reflectClass->getMethods() as $method ){
                   $method_name=$method->name;
                    //var_dump($method);
                Route::registerRoute($annotationObject->getName(),$annotationObject->getVersion(),$method_name,$this->className);
            }
         //返回当前类名注册到框架的bean容器当中
         return  [$this->className,$this->className,Bean::SINGLETON,''];
    }
}