<?php
/**
 * Created by PhpStorm.
 * User: leizhuochao
 * Date: 2017/3/3
 * Time: 下午2:27
 */

include "Util.php";
include "entity/Province.php";


$p = new province();
$p->id = 1;
$p->name = "测试省份";
$p->num = 3;


$t = new Database();


$result = $t->find($p,2,2);

//echo "\n".$result."\n";