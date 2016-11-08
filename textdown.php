<?php
		$filePath = "D:/WWW/amazon_crawl_system/data/";//此处给出你下载的文件在服务器的什么地方
		$fileName = "CrawlerID_text.txt";
	
		include_once('./mysql.php');
		$conndb=new ConnDB();
		$conndb->connect("127.0.0.1","root","root","amazon");
		
		
		$sql="select id from t_crawl_url where crawl_id=3";
		$request=$conndb->queryarr($sql);
		$str='';
		if($request){
			foreach($request as $key => $values){
				$str.="crawl_url_id='".$values['id']."' or ";
			}
			$str=substr($str,0,strlen($str)-4);
			echo $str;
		}
		
		$sql="select pid,price Into OutFile '".$filePath.$fileName."' fields terminated by ',' lines terminated by '\r\n' From `t_product` where ".$str;
		
		echo $sql;
		
		

?>