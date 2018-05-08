<?php
header("Content-type:application/json;charset=utf-8");
//引入配置文件
@include_once dirname(__DIR__).'/base.php';
$deviceId = '';
$id = '';
$fsym = '';
$icon = '';

$digital_relation = $table_prefix.'digital_relation';
if(isset($_POST['deviceId']))
{
	$deviceId = trim($_POST['deviceId']);
}

if(empty($deviceId))
{
	exit(json_encode(['status'=>400,'info'=>'error','data'=>'need deviceId']));
}
if(isset($_POST['relation_id']))
{
	$id = (int) trim($_POST['relation_id']);
}
if(empty($id))
{
	exit(json_encode(['status'=>400,'info'=>'error','data'=>'need relation_id']));
}

if(isset($_POST['fsym']))
{
	$fsym = trim($_POST['fsym']);
}
if(isset($_POST['icon']))
{
	$icon = trim($_POST['icon']);
}

$pdo->exec("UPDATE $digital_relation SET `fsym`='{$fsym}',`icon`='{$icon}' WHERE relation_id=$id AND `deviceId`='{$deviceId}' ");
exit(json_encode(['status'=>200,'info'=>'success','data'=>'success']));