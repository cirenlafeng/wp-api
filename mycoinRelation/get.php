<?php
header("Content-type:application/json;charset=utf-8");
//引入配置文件
@include_once dirname(__DIR__).'/base.php';

$postOffset = 0;
$postLimit = 10;
$deviceId = '';
$digital_relation = $table_prefix.'digital_relation';
if(isset($_GET['offset']))
{
	$postOffset = (int) trim($_GET['offset']);
}

if(isset($_GET['limit']))
{
	$postLimit = (int) trim($_GET['limit']);
}

if(isset($_GET['deviceId']))
{
	$deviceId = trim($_GET['deviceId']);
}

if(empty($deviceId))
{
	exit(json_encode(['status'=>400,'info'=>'error','data'=>'need deviceId']));
}

$posts = $pdo->query("SELECT * FROM $digital_relation WHERE `deviceId`='{$deviceId}' ORDER BY relation_id desc LIMIT {$postOffset},{$postLimit}");
if($posts)
{
  $posts = $posts->fetchAll(PDO::FETCH_ASSOC);
}else{
  $posts = null;
}
$count = $pdo->query("SELECT count(1) as `count` FROM $digital_relation WHERE `deviceId`='{$deviceId}'")->fetch(PDO::FETCH_ASSOC);

foreach ($posts as $key => $value) {
	$posts[$key]['relation_id'] = (int) $value['relation_id'];
}
$data = [];
$data['relationList'] = $posts;
$data['listCount'] = (int) $count['count'];
echo json_encode(['status'=>200,'info'=>'success','data'=>$data]);