<?php
/**
 * Created by PhpStorm.
 * User: leizhuochao
 * Date: 2017/3/3
 * Time: 下午2:27
 */

include "Util.php";
include "entity/Province.php";


$province = new province();
$test_db_conn = new Database();


$results = $test_db_conn->find($province,1,1);

echo "\n";
//print_r($result[3]);
//foreach ($results as $result){
//    //echo $key."\t";
//    print_r($result);
//}

//for ($i = 0 ; $i < 10 ; $i++){
//    if (!$results[$i]) continue;
//    echo $i."\t";
//    print_r($results[$i]);
//}

$C = " create xxx";
$R = " select xxx";
$U = " update xxx";
$D = " delete xxx";

echo $test_db_conn->nativeSql($D);

echo "\n";