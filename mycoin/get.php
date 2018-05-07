<?php
header("Content-type:application/json;charset=utf-8");
//引入配置文件
@include_once dirname(__DIR__).'/base.php';

$postOffset = 0;
$postLimit = 10;
$uuid = '';
$digital_currency = $table_prefix.'digital_currency';
if(isset($_GET['offset']))
{
	$postOffset = (int) trim($_GET['offset']);
}

if(isset($_GET['limit']))
{
	$postLimit = (int) trim($_GET['limit']);
}

if(isset($_GET['uuid']))
{
	$uuid = trim($_GET['uuid']);
}

if(empty($uuid))
{
	exit(json_encode(['status'=>400,'info'=>'error','data'=>'need uuid']));
}

$posts = $pdo->query("SELECT * FROM $digital_currency WHERE `uuid`='{$uuid}' ORDER BY id desc LIMIT {$postOffset},{$postLimit}");
if($posts)
{
  $posts = $posts->fetchAll(PDO::FETCH_ASSOC);
}else{
  $posts = null;
}
$count = $pdo->query("SELECT count(1) as `count` FROM $digital_currency WHERE `uuid`='{$uuid}'")->fetch(PDO::FETCH_ASSOC);


$data = [];
$data['coinList'] = $posts;
$data['listCount'] = (int) $count['count'];
echo json_encode(['status'=>200,'info'=>'success','data'=>$data]);