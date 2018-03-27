<?php
//引入配置文件
@include_once dirname(__DIR__).'/wp-config.php';

$dbms='mysql';     //数据库类型
$host=DB_HOST; //数据库主机名
if($host == 'localhost')
{
	$host = '127.0.0.1';
}
$dbName=DB_NAME;    //使用的数据库
$user=DB_USER;      //数据库连接用户名
$pass=DB_PASSWORD;  //对应的密码
$dsn="$dbms:host=$host;dbname=$dbName";


try {
    $pdo = new PDO($dsn, $user, $pass); //初始化PDO

} catch (PDOException $e) {
    die ("Error!: can not find database");
}