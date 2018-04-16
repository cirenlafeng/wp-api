<?php
header("Content-type:application/json;charset=utf-8");

@include_once 'base.php';

$postOffset = 0;
$postLimit = 10;
$keyword = '';

if(isset($_GET['postOffset']))
{
	$postOffset = (int) trim($_GET['postOffset']);
}

if(isset($_GET['postLimit']))
{
	$postLimit = (int) trim($_GET['postLimit']);
}

if(isset($_GET['keyword']))
{
	$keyword =  trim($_GET['keyword']);
}else{
	exit(json_encode(['status'=>400,'info'=>'error','data'=>'need keyword']));
}



$table_post = $table_prefix.'posts';
$table_term_relationships = $table_prefix.'term_relationships';
$table_postmeta = $table_prefix.'postmeta';

function catch_that_image($post_id) {
   global $pdo,$table_postmeta,$table_post;
   $row = $pdo->query("SELECT * FROM $table_postmeta WHERE `post_id` = $post_id AND `meta_key` = '_thumbnail_id' Limit 1;")->fetch(PDO::FETCH_ASSOC);
   if($row)
   {
      $row2 = $pdo->query("SELECT * FROM $table_post WHERE `ID` = {$row['meta_value']} Limit 1;")->fetch(PDO::FETCH_ASSOC);
      if($row2)
      {
         return $row2['guid'];
      }else{
         return "";
      }
   }else{
      return "";
   }
}

$field = '`ID`,`post_title`,`post_content`,`post_date_gmt`,`post_mime_type`';
$row = $pdo->prepare("SELECT $field FROM $table_post WHERE post_title like :keyword AND (post_status='publish') ORDER BY ID desc LIMIT {$postOffset},{$postLimit}");
if($row->execute(array('keyword' => '%'.$keyword.'%')))
{
	$posts = $row->fetchAll(PDO::FETCH_ASSOC);
}else{
	$posts = (object) [];
}
foreach ($posts as $key => $value) {
	$posts[$key]['first_img'] = catch_that_image($value['ID']);
	unset($posts[$key]['post_content']);
}
$count = $pdo->prepare("SELECT count(1) as `count` FROM $table_post WHERE post_title like :keyword AND (post_status='publish')");
if($count->execute(array('keyword' => '%'.$keyword.'%')))
{
	$count = $count->fetch(PDO::FETCH_ASSOC);
}else{
	$count = [];
	$count['count'] = 0;
}

$data = [];
$data['posts'] = $posts;
$data['postsCount'] = (int) $count['count'];
echo json_encode(['status'=>200,'info'=>'success','data'=>$data]);