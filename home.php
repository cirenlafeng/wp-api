<?php
header("Content-type:application/json;charset=utf-8");
error_reporting(-1);
ini_set('display_errors', 1);
@include_once 'base.php';


$carouselLimit = 5;
$postOffset = 0;
$postLimit = 10;
if(isset($_GET['postOffset']))
{
	$postOffset = (int) trim($_GET['postOffset']);
}

if(isset($_GET['postLimit']))
{
	$postLimit = (int) trim($_GET['postLimit']);
}



$table_post = $table_prefix.'posts';
$table_option = $table_prefix.'options';
$field = '`ID`,`post_title`,`post_content`,`post_date_gmt`,`post_mime_type`';
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

$ids = $pdo->query("SELECT * FROM $table_option WHERE option_name = 'banner_custom_ids' Limit 1;")->fetch(PDO::FETCH_ASSOC);
if($ids)
{
	$carousel = $pdo->query("SELECT {$field} FROM $table_post WHERE ID IN({$ids['option_value']}) ORDER BY ID desc LIMIT {$carouselLimit}")->fetchAll(PDO::FETCH_ASSOC);
   foreach ($carousel as $skey => $svalue) {
      $carousel[$skey]['first_img'] = catch_that_image($svalue['ID']);
   }
}else{
	$carousel = null;
}

$posts = $pdo->query("SELECT {$field} FROM $table_post WHERE (post_status='publish') ORDER BY ID desc LIMIT {$postOffset},{$postLimit}")->fetchAll(PDO::FETCH_ASSOC);
$count = $pdo->query("SELECT count(1) as `count` FROM $table_post WHERE (post_status='publish')")->fetch(PDO::FETCH_ASSOC);
foreach ($posts as $key => $value) {
	$posts[$key]['first_img'] = catch_that_image($value['ID']);
   unset($posts[$key]['post_content']);
}
$data = [];
$data['carousel'] = $carousel;
$data['posts'] = $posts;
$data['postsCount'] = (int) $count['count'];
echo json_encode(['status'=>200,'info'=>'success','data'=>$data]);


