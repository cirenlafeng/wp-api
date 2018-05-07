<?php
header("Content-type:application/json;charset=utf-8");
date_default_timezone_set('Asia/Riyadh');
//引入配置文件
@include_once dirname(__DIR__).'/base.php';
$uuid = '';
$coinName = '';
$coinIcon = '';
$costPrice = '';
$costNumber = '';
$info = '';

$digital_currency = $table_prefix.'digital_currency';
if(isset($_POST['uuid']))
{
	$uuid = trim($_POST['uuid']);
}

if(empty($uuid))
{
	exit(json_encode(['status'=>400,'info'=>'error','data'=>'need uuid']));
}

if(isset($_POST['coinName']))
{
	$coinName = trim($_POST['coinName']);
}
if(isset($_POST['coinIcon']))
{
	$coinIcon = trim($_POST['coinIcon']);
}
if(isset($_POST['costPrice']))
{
	$costPrice = trim($_POST['costPrice']);
}
if(isset($_POST['costNumber']))
{
	$costNumber = trim($_POST['costNumber']);
}
if(isset($_POST['info']))
{
	$info = trim($_POST['info']);
}

$date = date('Y-m-d H:i:s',time());
$pdo->exec("INSERT INTO $digital_currency (`uuid`,`coinName`,`coinIcon`,`costPrice`,`costNumber`,`date`,`info`) VALUES('{$uuid}','{$coinName}','{$coinIcon}','{$costPrice}','{$costNumber}','{$date}','{$info}')");
exit(json_encode(['status'=>200,'info'=>'success','data'=>'success']));