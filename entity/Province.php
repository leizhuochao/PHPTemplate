<?php

/**
 * Created by PhpStorm.
 * User: leizhuochao
 * Date: 2017/3/3
 * Time: 下午2:38
 */

class Province
{
    private $id;
    private $name;
    private $info;
    private $num;

    function __get($prop_name)
    {
        // TODO: Implement __get() method.
        return $this->$prop_name;
    }

    function __set($prop_name, $prop_value)
    {
        // TODO: Implement __set() method.
        $this->$prop_name = $prop_value;
    }
}