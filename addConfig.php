<?php

header("Content-Type:text/html;charset=utf-8");
include_once('./mysql.php');
$conndb=new ConnDB();

if(isset($_POST['confname'])&&isset($_POST['lowprice'])&&isset($_POST['status'])){
	$confame=$_POST['confname'];
	$lowprice=$_POST['lowprice'];
	$status=$_POST['status'];
}

$sql="insert into t_crawl_configuration(name,lowest_price,create_time,ischange) values('$confame','$lowprice',NOW(),'$status')";

$request=$conndb->query($sql);


if($request)
{
	$arr['success']=1;
	$arr['msg']='Login Success';
}
else
{
	$arr['error']=mysql_error();
	$arr['success']=0;
	$arr['msg']='Login Failed';
}
/*$arr['success']=1;
$arr['msg']='Login success';*/
echo json_encode($arr);
?>