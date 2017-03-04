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
$p->id = 2;
$p->name = "测试省份";
$p->info = "message";


$t = new Database();


$result = $t->insert($p);

echo $result;