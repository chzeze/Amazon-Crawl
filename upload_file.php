<?php
header("Content-Type:text/html;charset=utf-8");
//保存上传的文件
$time=date("YmdHis");
if(isset($_POST['crawlid'])){
	$crawlid=$_POST['crawlid'];
	/*echo "<script> alert($crawlid);</script>";*/
}
if ((($_FILES["file"]["type"] == "text/plain"))) {//可以限制上传的大小
    if ($_FILES["file"]["error"] > 0) {
        echo "Return Code: ".$_FILES["file"]["error"].
        "<br />";
    } else {
		$curfile="upload/".$time.".txt";
        echo "Upload: ".$_FILES["file"]["name"].
        "<br />";
        echo "Type: ".$_FILES["file"]["type"].
        "<br />";
        echo "Size: ".($_FILES["file"]["size"] / 1024).
        " Kb<br />";
        echo "Temp file: ".$_FILES["file"]["tmp_name"].
        "<br />";
        if (file_exists($curfile)) {
            echo $curfile." already exists. ";
			echo "<script> alert($curfile+' already exists. ');window.location.href='index.php';</script>";
        } else {
            move_uploaded_file($_FILES["file"]["tmp_name"], $curfile);
            echo "Stored in: ".$curfile."<br />";
			
			$strSQL = "load data infile 'D:/WWW/amazon/".$curfile."'". " into table t_crawl_url_temp LINES TERMINATED BY '\r\n' STARTING BY 'http' (url) set crawl_id=".$crawlid;
			//echo $strSQL;
			include_once('./mysql.php');
			$conndb=new ConnDB();
			$request=$conndb->query($strSQL);
			echo $strSQL;
			if($request)
			{
				echo "<script> alert('文件上传成功!');window.location.href=document.referrer;</script>";
			}
			else
			{
				echo "<script> alert('文件上传失败!');window.location.href=document.referrer;</script>";
			}     
        }
    }
} else {
	echo "Type: ".$_FILES["file"]["type"]."<br />";
    echo "Invalid file";
	echo "<script> alert('无效文件，请上传txt类型文件!');window.location.href=document.referrer;</script>";
}
?>