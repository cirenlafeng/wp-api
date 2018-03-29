<?php
header("Content-type:application/json;charset=utf-8");

@include_once 'base.php';

$device_id = 0;
$post_id = 0;
$type = 1;
//类型默认1:有帮助，2没有帮助

if(isset($_GET['device_id']))
{
	$device_id = (string) trim($_GET['device_id']);
}else{
	exit(json_encode(['status'=>400,'info'=>'error','data'=>'need device_id']));
}

if(isset($_GET['post_id']))
{
	$post_id = (int) trim($_GET['post_id']);
}else{
	exit(json_encode(['status'=>400,'info'=>'error','data'=>'need post_id']));
}

if(isset($_GET['type']))
{
	$type = (int) trim($_GET['type']);
}

$checkIsSet = $pdo->query("SELECT * FROM `wp_posts_helped` WHERE post_id = $post_id AND device_id='{$device_id}'")->fetch(PDO::FETCH_ASSOC);
if($checkIsSet)
{
	exit(json_encode(['status'=>401,'info'=>'repeat','data'=>'you have voted']));
}

if($pdo->exec("INSERT INTO `wp_posts_helped` VALUES({$post_id},'{$device_id}',{$type})"))
{
	$table_postmeta = $table_prefix.'postmeta';
	if($type == 1){
		$pdo->exec("UPDATE $table_postmeta SET meta_value = meta_value + 1 WHERE post_id={$post_id} AND meta_key='ApiMeta_helped'");
	}else{
		$pdo->exec("UPDATE $table_postmeta SET meta_value = meta_value + 1 WHERE post_id={$post_id} AND meta_key='ApiMeta_unHelped'");
	}
}
exit(json_encode(['status'=>200,'info'=>'success','data'=>'vote success']));