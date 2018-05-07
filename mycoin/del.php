<?php
header("Content-type:application/json;charset=utf-8");
//引入配置文件
@include_once dirname(__DIR__).'/base.php';
$uuid = '';
$id = '';
$digital_currency = $table_prefix.'digital_currency';
if(isset($_POST['uuid']))
{
	$uuid = trim($_POST['uuid']);
}
if(empty($uuid))
{
	exit(json_encode(['status'=>400,'info'=>'error','data'=>'need uuid']));
}

if(isset($_POST['id']))
{
	$id = (int) trim($_POST['id']);
}
if(empty($id))
{
	exit(json_encode(['status'=>400,'info'=>'error','data'=>'need id']));
}

$pdo->exec("DELETE FROM $digital_currency WHERE id=$id AND `uuid`='{$uuid}' ");
exit(json_encode(['status'=>200,'info'=>'success','data'=>'success']));