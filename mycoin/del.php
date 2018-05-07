<?php
header("Content-type:application/json;charset=utf-8");
//引入配置文件
@include_once dirname(__DIR__).'/base.php';
$deviceId = '';
$id = '';
$digital_currency = $table_prefix.'digital_currency';
if(isset($_POST['deviceId']))
{
	$deviceId = trim($_POST['deviceId']);
}
if(empty($deviceId))
{
	exit(json_encode(['status'=>400,'info'=>'error','data'=>'need deviceId']));
}

if(isset($_POST['id']))
{
	$id = (int) trim($_POST['id']);
}
if(empty($id))
{
	exit(json_encode(['status'=>400,'info'=>'error','data'=>'need id']));
}

$pdo->exec("DELETE FROM $digital_currency WHERE id=$id AND `deviceId`='{$deviceId}' ");
exit(json_encode(['status'=>200,'info'=>'success','data'=>'success']));