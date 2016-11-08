<?php

	//下载数据
	header('Content-Type:text/html;charset=utf-8');
	$crawlId=$_GET['id'];
	$price=$_GET['price'];//最低价格
	$name=urldecode($_GET['name']);
	$name=iconv("utf-8","gb2312",$name);//解决乱码
	$time=date("Y-m-d-H-i-s");
	
	//导出sql语句：Select pid,price Into OutFile 'F:/QQPCmgr/Desktop/Data_OutFile.txt' fields terminated by ',' From `t_product`
	//导出文件命名格式Crawl_ID_txt;
	$filePath = "D:/WWW/amazon/data/";//此处给出你下载的文件在服务器的什么地方
	$fileName = $name."_".$time.".txt";
	
	//删除文件
	if(file_exists($filePath.$fileName)){
		unlink($filePath.$fileName);
	}
	
	include_once('./mysql.php');
	$conndb=new ConnDB();
	
	//首先从t_crawl_url表中获取对应的id值，拼接sql语句
	$sql="select id from t_crawl_url where crawl_id=".$crawlId;
	$request=$conndb->queryarr($sql);
	$str='';
	if($request){
		foreach($request as $key => $values){
			$str.="crawl_url_id='".$values['id']."' and price>='".$price."' or ";
		}
		$str=substr($str,0,strlen($str)-4);
		//echo $str;
	}else{
		echo "error";
		exit;
	}
	
	$sql="select pid,price Into OutFile '".$filePath.$fileName."' fields terminated by ',' lines terminated by '\r\n' From `t_product` where ".$str;
	//echo $sql;
	$request=$conndb->query($sql);
	if($request){
		/*echo "<script> window.location.href='download.php?id=".$crawlId."&file=".$saveFile."';</script>";*/
	}else{
		echo "<script> alert('下载失败');</script>";
		exit;
	}
	
	//此处给出你下载的文件名
	$file = fopen($filePath.$fileName, "r"); //打开文件
	//输入文件标签
	header("Content-type:application/octet-stream ");
	header("Accept-Ranges:bytes ");
	header("Accept-Length:   " . filesize($filePath.$fileName));
	header("Content-Disposition:   attachment;   filename= ".$fileName);
	
	//输出文件内容
	echo fread($file, filesize($filePath . $fileName));
	fclose($file);
	

?>