<?php declare(strict_types=1);


namespace Six\Rpc\Client;

use Swoft\Rpc\Client\Exception\RpcClientException;

/**
 * Class ReferenceRegister
 *
 * @since 2.0
 */
class ReferenceRegister
{
    /**
     * @var array
     *
     * @example
     * [
     *     'className' => [
     *         'pool' => 'poolName',
     *         'version' => 'version',
     *         'fallback'=> 'fallback'
     *     ]
     * ]
     */
    private static $references = [];

    /**
     * @param string $className
     * @param string $pool
     * @param string $version
     */
    public static function register(string $className, string $pool, string $version,string $fallback)
    {
           self::$references[$className]['pool']    = $pool;
           self::$references[$className]['version'] = $version;
           self::$references[$className]['fallback'] = $fallback;
    }

    /**
     * @param string $className
     *
     * @return string
     * @throws RpcClientException
     */
    public static function getPool(string $className): string
    {
        $pool = self::$references[$className]['pool'] ?? '';
        if (empty($pool)) {
            throw new RpcClientException(
                sprintf('`@Reference` pool (%s) is not exist!', $className)
            );
        }
        return $pool;
    }

    /**
     * @param string $className
     *
     * @return string
     * @throws RpcClientException
     */
    public static function getVersion(string $className): string
    {
        $version = self::$references[$className]['version'] ?? '';
        if (empty($version)) {
            throw new RpcClientException(
                sprintf('`@Reference` version(%s) is not exist!', $className)
            );
        }

        return $version;
    }

    public static function getFallback(string $className): string
    {
        $fallback = self::$references[$className]['fallback'] ?? '';
        if (empty($fallback)) {
            throw new RpcClientException(
                sprintf('`@Reference` fallback(%s) is not exist!', $className)
            );
        }
        return $fallback;
    }
}