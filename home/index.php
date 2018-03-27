<?php
header("Content-type:application/json");
error_reporting(E_ALL);
@include_once dirname(__DIR__).'\base.php';


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

$ids = $pdo->query("SELECT * FROM $table_option WHERE option_name = 'banner_custom_ids' Limit 1;")->fetch(PDO::FETCH_ASSOC);
if($ids)
{
	$carousel = $pdo->query("SELECT {$field} FROM $table_post WHERE ID IN({$ids['option_value']}) ORDER BY ID desc LIMIT {$carouselLimit}")->fetchAll(PDO::FETCH_ASSOC);
}else{
	$carousel = null;
}

$posts = $pdo->query("SELECT {$field} FROM $table_post WHERE (post_status='inherit' OR post_status='publish') ORDER BY ID desc LIMIT {$postOffset},{$postLimit}")->fetchAll(PDO::FETCH_ASSOC);
$count = $pdo->query("SELECT count(1) as `count` FROM $table_post WHERE (post_status='inherit' OR post_status='publish')")->fetch(PDO::FETCH_ASSOC);
foreach ($posts as $key => $value) {
	$posts[$key]['first_img'] = catch_that_image($value['post_content']);
}
$data = [];
$data['carousel'] = $carousel;
$data['posts'] = $posts;
$data['postsCount'] = $count['count'];
exit(json_encode(['status'=>200,'info'=>'success','data'=>$data]));

function catch_that_image($post_content) {
   $first_img = '';
   ob_start();
   ob_end_clean();
   $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post_content, $matches);
   $first_img = '';
   if(empty($matches[1])) $first_img = "";
   else $first_img = $matches [1][0];
   return $first_img;
}
