<?php
header("Content-type:application/json;charset=utf-8");
date_default_timezone_set('Asia/Riyadh');
//引入配置文件
@include_once dirname(__DIR__).'/base.php';
$deviceId = '';
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

if(isset($_POST['fsym']))
{
	$fsym = trim($_POST['fsym']);
}
if(isset($_POST['icon']))
{
	$icon = trim($_POST['icon']);
}

$pdo->exec("INSERT INTO $digital_relation (`deviceId`,`fsym`,`icon`) VALUES('{$deviceId}','{$fsym}','{$icon}')");
exit(json_encode(['status'=>200,'info'=>'success','data'=>'success']));