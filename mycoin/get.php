<?php
header("Content-type:application/json;charset=utf-8");
//引入配置文件
@include_once dirname(__DIR__).'/base.php';

$deviceId = '';
$digital_currency = $table_prefix.'digital_currency';

if(isset($_GET['deviceId']))
{
	$deviceId = trim($_GET['deviceId']);
}

if(empty($deviceId))
{
	exit(json_encode(['status'=>400,'info'=>'error','data'=>'need deviceId']));
}

$posts = $pdo->query("SELECT * FROM $digital_currency WHERE `deviceId`='{$deviceId}' ORDER BY id desc ");
if($posts)
{
  $posts = $posts->fetchAll(PDO::FETCH_ASSOC);
}else{
  $posts = null;
}
$count = $pdo->query("SELECT count(1) as `count` FROM $digital_currency WHERE `deviceId`='{$deviceId}'")->fetch(PDO::FETCH_ASSOC);

foreach ($posts as $key => $value) {
	$posts[$key]['id'] = (int) $value['id'];
}
$data = [];
$data['coinList'] = $posts;
$data['listCount'] = (int) $count['count'];
echo json_encode(['status'=>200,'info'=>'success','data'=>$data]);