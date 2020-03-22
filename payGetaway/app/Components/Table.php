<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/7/13
 * Time: 22:25
 */

namespace App\Componentes;


class Table {
    private static $tables;
    private static $instance;
    private function __construct ()
    {
    }
    public static function  get_instance(){
        if(is_null (self::$instance)){
            self::$instance = new self();
        }
        return self::$instance;
    }

    public  function  add($name,$columns,$size = 1024){

        self::$tables[$name]= new \Swoole\Table($size); //表行数
        foreach ($columns as $k=>$v){
            self::$tables[$name] ->column($k, $v['type'],$v['size']);
        }
        self::$tables[$name]->create();
    }

    public  function  get($name){
         return   self::$tables[$name]??'';
    }
}