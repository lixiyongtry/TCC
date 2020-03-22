<?php
/**
 * Created by PhpStorm.
 * User: Sixstar-Peter
 * Date: 2019/6/12
 * Time: 22:24
 */

namespace Six\Rpc\Client;


class Route
{
    /**
     * @var array
     * [
     *
     * ]
     */
    private static $routeList = [];

    public static function registerRoute($fallback, $version, $method_name, $className)
    {
        self::$routeList[$fallback][$version][$method_name] = $className;
    }

    public static function match($fallback, $version, $method_name)
    {
        return self::$routeList[$fallback][$version][$method_name] ?? "";
    }
}