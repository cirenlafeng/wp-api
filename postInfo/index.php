<?php
header("Content-type:application/json");
@include_once dirname(__DIR__).'\base.php';

if(isset($_GET['ID']))
{
   $ID = (int) trim($_GET['ID']);
}else{
   exit(json_encode(['status'=>400,'info'=>'error','data'=>'need post ID']));
}

$table_post = $table_prefix.'posts';
$table_author = $table_prefix.'users';
$table_tag = $table_prefix.'terms';
$table_tag_relationships = $table_prefix.'term_relationships';
$table_postmeta = $table_prefix.'postmeta';
$table_term_taxonomy = $table_prefix.'term_taxonomy';

$field = '`ID`,`post_title`,`post_content`,`post_date_gmt`,`post_mime_type`,`post_author`';


$post = $pdo->query("SELECT {$field} FROM $table_post WHERE ID={$ID} AND (post_status='inherit' OR post_status='publish') LIMIT 1")->fetch(PDO::FETCH_ASSOC);

$data = [];
$data['post'] = $post;
$data['author'] = getAuthor($post['post_author']);
$data['tags'] = getTags($post['ID']);
$data['btc_price'] = getBtcPrice($post['ID']);
$data['helped'] = gethelped($post['ID']);
$data['unhelped'] = getUnHelped($post['ID']);
$data['RecommendArticles'] = getRecommendArticles($post['ID']);

exit(json_encode(['status'=>200,'info'=>'success','data'=>$data]));

function getAuthor($authorId)
{
   global $table_author,$pdo;
   $author = $pdo->query("SELECT `ID`,`user_nicename` FROM $table_author WHERE ID={$authorId} AND user_status='0' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
   return $author;
}

function getTags($ID)
{
	global $table_tag,$pdo,$table_tag_relationships;
	$tags = $pdo->query("SELECT * FROM $table_tag_relationships WHERE object_id={$ID}")->fetchAll(PDO::FETCH_ASSOC);
	$tagIds = '';
	foreach ($tags as $key => $value) {
		$tagIds.= ','.$value['term_taxonomy_id'];
	}
	$tagIds = trim($tagIds,',');
	if($row = $pdo->query("SELECT * FROM $table_tag WHERE term_id IN ({$tagIds})"))
	{
		$tagObj = $row->fetchAll(PDO::FETCH_ASSOC);
	}else{
		$tagObj = (object) [];
	}
	
	return $tagObj;
}

function getBtcPrice($ID)
{
	global $pdo,$table_postmeta;
	$price = $pdo->query("SELECT * FROM $table_postmeta WHERE post_id={$ID} AND meta_key='ApiMeta_btc_price' ")->fetch(PDO::FETCH_ASSOC);
	if($price){
		$price = $price['meta_value'];
	}else{
		$price = '0.00';
	}
	return $price;
}

function gethelped($ID)
{
	global $pdo,$table_postmeta;
	$helped = $pdo->query("SELECT * FROM $table_postmeta WHERE post_id={$ID} AND meta_key='ApiMeta_helped' ")->fetch(PDO::FETCH_ASSOC);
	if($helped){
		$helped = (int) $helped['meta_value'];
	}else{
		$helped = 0;
	}
	return $helped;
}

function getUnHelped($ID)
{
	global $pdo,$table_postmeta;
	$helped = $pdo->query("SELECT * FROM $table_postmeta WHERE post_id={$ID} AND meta_key='ApiMeta_unHelped' ")->fetch(PDO::FETCH_ASSOC);
	if($helped){
		$helped = (int) $helped['meta_value'];
	}else{
		$helped = 0;
	}
	return $helped;
}

function getRecommendArticles($ID)
{
	global $pdo,$table_tag_relationships,$table_post,$field;
	$tags = $pdo->query("SELECT * FROM $table_tag_relationships WHERE object_id={$ID}")->fetchAll(PDO::FETCH_ASSOC);
	$tagIds = '';
	foreach ($tags as $key => $value) {
		$tagIds.= ','.$value['term_taxonomy_id'];
	}
	$tagIds = trim($tagIds,',');
	if($row = $pdo->query("SELECT * FROM $table_tag_relationships WHERE term_taxonomy_id IN ({$tagIds}) AND object_id <> {$ID} GROUP BY object_id ORDER BY rand() LIMIT 5"))
	{
		$ids = $row->fetchAll(PDO::FETCH_ASSOC);
	}else{
		$ids = [];
	}
	$Ids = '';
	foreach ($ids as $key => $value) {
		$Ids.= ','.$value['object_id'];
	}
	$Ids = trim($Ids,',');
	if($ps = $pdo->query("SELECT $field FROM $table_post WHERE ID IN ({$Ids}) LIMIT 5"))
	{
		$posts = $ps->fetchAll(PDO::FETCH_ASSOC);
	}else{
		$posts = (object) [];
	}
	return $posts;
}
