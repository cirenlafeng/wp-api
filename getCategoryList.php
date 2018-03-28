<?php
header("Content-type:application/json;charset=utf-8");

@include_once 'base.php';

$table_term_taxonomy = $table_prefix.'term_taxonomy';
$table_terms = $table_prefix.'terms';

$row = $pdo->query("SELECT * FROM $table_term_taxonomy WHERE taxonomy='category' AND parent=0")->fetchAll(PDO::FETCH_ASSOC);
$tagIds = '';
foreach ($row as $key => $value) {
	$tagIds.= ','.$value['term_id'];
}
$tagIds = trim($tagIds,',');

if($row = $pdo->query("SELECT * FROM $table_terms WHERE term_id IN ({$tagIds})"))
{
	$terms = $row->fetchAll(PDO::FETCH_ASSOC);
}else{
	$terms = (object) [];
}

$data = [];
$data['terms'] = $terms;
echo json_encode(['status'=>200,'info'=>'success','data'=>$data]);