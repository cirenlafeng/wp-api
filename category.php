<?php
header("Content-type:application/json;charset=utf-8");

@include_once 'base.php';

$postOffset = 0;
$postLimit = 10;
$categoryId = 0;

if(isset($_GET['postOffset']))
{
	$postOffset = (int) trim($_GET['postOffset']);
}

if(isset($_GET['postLimit']))
{
	$postLimit = (int) trim($_GET['postLimit']);
}

if(isset($_GET['term_id']))
{
	$categoryId = (int) trim($_GET['term_id']);
}else{
	exit(json_encode(['status'=>400,'info'=>'error','data'=>'need term_id']));
}

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

$table_post = $table_prefix.'posts';
$table_term_relationships = $table_prefix.'term_relationships';

$field = '`ID`,`post_title`,`post_content`,`post_date_gmt`,`post_mime_type`';

$row = $pdo->query("SELECT * FROM $table_term_relationships WHERE term_taxonomy_id={$categoryId}")->fetchAll(PDO::FETCH_ASSOC);
$tagIds = '';
foreach ($row as $key => $value) {
	$tagIds.= ','.$value['object_id'];
}
$tagIds = trim($tagIds,',');
if($row = $pdo->query("SELECT $field FROM $table_post WHERE ID IN ({$tagIds}) AND (post_status='publish') ORDER BY ID desc LIMIT {$postOffset},{$postLimit}"))
{
	$posts = $row->fetchAll(PDO::FETCH_ASSOC);
}else{
	$posts = (object) [];
}
foreach ($posts as $key => $value) {
	$posts[$key]['first_img'] = catch_that_image($value['post_content']);
	unset($posts[$key]['post_content']);
}
$count = $pdo->query("SELECT count(1) as `count` FROM $table_post WHERE ID IN ({$tagIds}) AND (post_status='publish')")->fetch(PDO::FETCH_ASSOC);
$data = [];
$data['posts'] = $posts;
$data['postsCount'] = (int) $count['count'];
echo json_encode(['status'=>200,'info'=>'success','data'=>$data]);