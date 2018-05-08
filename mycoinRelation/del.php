<?php
header("Content-type:application/json;charset=utf-8");
//引入配置文件
@include_once dirname(__DIR__).'/base.php';
$deviceId = '';
$fsym = '';
$digital_relation = $table_prefix.'digital_relation';
if(isset($_POST['deviceId']))
{
	$deviceId = trim($_POST['deviceId']);
}
if(empty($deviceId))
{
	exit(json_encode(['status'=>400,'info'=>'error','data'=>'need deviceId']));
}

if(isset($_POST['fsym']))
{
	$fsym = (int) trim($_POST['fsym']);
}
if(empty($fsym))
{
	exit(json_encode(['status'=>400,'info'=>'error','data'=>'need fsym']));
}

$pdo->exec("DELETE FROM $digital_relation WHERE `fsym`='{$fsym}' AND `deviceId`='{$deviceId}' ");
exit(json_encode(['status'=>200,'info'=>'success','data'=>'success']));