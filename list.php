<!DOCTYPE >
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Amazon Crawl System</title>
<link rel="stylesheet" type="text/css" href="./css/style.css" />
<script src="./js/jquery-1.8.3.min.js"></script>
</head>

<body id="nv_plugin" class="pg_freeget" onkeydown="if(event.keyCode==27) return false;">
<div id="wp" class="wp">

<script>
$(document).ready(function(){
	
	var mysearch=GetQueryString("viewkey");
	if(mysearch !=null && mysearch.length>0 && mysearch!='null'){
		$("#crawlid").val(mysearch);
	}
	$("#submit").click(function(){
	  var search=$("#search").val();
	  window.location.href='index.php?search='+encodeURI(encodeURI(search)); ;
	});
	function GetQueryString(name){
     var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
     var r = window.location.search.substr(1).match(reg);
     if(r!=null)return  unescape(r[2]); return null;
	}
	
});


</script>
<div id="freeget">
<div class="wp fuck_h mb20">
<div>
  <div class="input-group" id="url-input">
 
    <table width="100%" class="table">
      <caption align="top" style="border: 1px solid #cad9ea;padding: 0 0.5em 0;border-bottom: none;background-color: #EDEDED;line-height: 45px;font-size: 25px;font-weight: 600;text-align:center;color: #2770C0;font-family: microsoft Yahei;">
      <button style="position: absolute;margin-top: 1%;right: 10%;" onClick="window.location.href='index.php'">返回首页</button>
      <a href="index.php" style="text-decoration:none">采集配置Url列表</a>
      <div  style="padding-bottom:10px;">
         <!-- 上传文件 model start-->
      <form action="upload_file.php" method="post" enctype="multipart/form-data" style="margin-bottom: 0px;">
        <label for="file" style="font-size:14px">选择上传的文件:</label>
        <input type="file" name="file" id="file" />
        <input type="submit" name="submit" value="上传文件" />
        <input type="hidden" name='crawlid' id='crawlid' value="">
      </form>
      <div style="font-size:12px; color:#F00; height:30px;">
      (Tips：上传文件格式为txt文件，每一行配置一条待采集url。)
      </div>
     
    <!-- 上传文件 model end-->
      </div>
      </caption>
      
      <?php
        	include_once('./mysql.php');
			$conndb=new ConnDB();
			
			//获取消息总数
			$sql="select count(*) from t_crawl_url";
			if(isset($_GET["viewkey"])){//存在搜索
				$sql="select count(*) from t_crawl_url where crawl_id='".$_GET["viewkey"]."'";
			}
			$arr=$conndb->queryarr($sql);
			//查询temp表
			$sql="select count(*) from t_crawl_url_temp";
			if(isset($_GET["viewkey"])){//存在搜索
				$sql="select count(*) from t_crawl_url_temp where crawl_id='".$_GET["viewkey"]."'";
			}
			$arr1=$conndb->queryarr($sql);
			
			$total=$arr[0][0]+$arr1[0][0];
			
			//根据每页的显示数计算页数
			$pagesize = 20;
			$PageCount = ceil($total/ $pagesize);
			if(isset($_GET["page"]))
			{
				$Page = intval($_GET["page"]);
				if($Page<1) 
					$Page=1;
			}
			else
			{
				$Page=1;
			}
			//根据当前页获取数据库
			$currentRow = empty($_GET['page']) ? 0 : ($_GET['page']-1)* $pagesize;
			$sql="select * from t_crawl_url  order by id asc limit $currentRow, $pagesize";
			//echo $sql;
			if(isset($_GET["viewkey"])){
				$sql="select * from t_crawl_url where crawl_id='".$_GET["viewkey"]."' order by id asc limit $currentRow, $pagesize";
			}
			//先从Url表中读取，为空再读缓存表
			$request1=$conndb->queryarr($sql);
			//var_dump($request);
			//if(count($request1)==0){
				$sql="select * from t_crawl_url_temp where crawl_id='".$_GET["viewkey"]."' order by id asc limit $currentRow, $pagesize";
				$request2=$conndb->queryarr($sql);
				//var_dump($request);
			//}
		?>
      <tr>
        <th width="616" style="width: 5%;"><div align="center">ID</div></th>
        <th width="182" style="width: 45%;"><div align="center">Url</div></th>
      <!-- <th width="135" style="width: 10%;"><div align="center">期望采集数</div></th>
      <th width="135" style="width: 10%;"><div align="center">当前采集数</div></th>-->
        <th width="135" style="width: 10%;"><div align="center">无变体状态</div></th>
        <th width="135" style="width:10%"><div align="center">变体状态</div></th>
      </tr>
      <?php     
			foreach($request2 as $key => $values){
		?>
      <tr id="del_49861">
        <td>tmp:<?=$values['id']?></td>
        <td><a id="e_vid" title="http<?=$values['url']?>" href="http<?=$values['url']?>" target="_blank" style="text-decoration:none; color:#666"><?php if(mb_strlen($values['url'])>90) echo 'http'.mb_substr($values['url'],0,110,'utf-8')."....."; else echo 'http'.$values['url'];?></a></td>
       <?php /*?><td align="center"><?php if($values['expect_count']=='') echo '等待统计...';  else echo $values['expect_count'];?></td>
        <td align="center"><?php if($values['products_count']=='') echo '等待统计...';  else echo $values['products_count'];?></td><?php */?>
        <td>
        <div id="b" class="b2r" align="center">
        <?php
		if($values['status']=='1'){
			echo "<div style='color:blue'>正在采集</div>";
        }else if($values['status']=='2'){
			echo "<div style='color:blue'>采集完成，等待抽取</div>";
        }else if($values['status']=='3'){
			echo "<div style='color:red'>正在抽取</div>";
        }else if($values['status']=='4'){
			echo "<div style='color:red'>抽取结束</div>";
        }else if($values['status']=='0'){
			echo "<div style='color:red'>等待采集</div>";
        }
        ?>
        </div>
        </td>
        <td>
        <div id="d" class="b2r" align="center">
        <?php
			if($values['status2']=='1'){
			echo "<div style='color:blue'>正在采集</div>";
			}else if($values['status2']=='2'){
				echo "<div style='color:blue'>采集完成，等待抽取</div>";
			}else if($values['status2']=='3'){
				echo "<div style='color:red'>正在抽取</div>";
			}else if($values['status2']=='4'){
				echo "<div style='color:red'>抽取结束</div>";
			}else if($values['status2']=='0'){
				echo "<div style='color:red'>等待采集</div>";
			}
        ?>
        </div>
        </td>
        
      </tr>
      <?php
  		}	
		?>
       
	   <?php     
			foreach($request1 as $key => $values){
		?>
      <tr id="del_49861">
        <td><?=$values['id']?></td>
        <td><a id="e_vid" title="<?=$values['url']?>" href="<?=$values['url']?>" target="_blank" style="text-decoration:none; color:#666"><?php if(mb_strlen($values['url'])>90) echo mb_substr($values['url'],0,110,'utf-8')."....."; else echo $values['url'];?></a></td>
       <?php /*?><td align="center"><?php if($values['expect_count']=='') echo '等待统计...';  else echo $values['expect_count'];?></td>
        <td align="center"><?php if($values['products_count']=='') echo '等待统计...';  else echo $values['products_count'];?></td><?php */?>
        <td>
        <div id="b" class="b2r" align="center">
        <?php
		if($values['status']=='1'){
			echo "<div style='color:blue'>正在采集</div>";
        }else if($values['status']=='2'){
			echo "<div style='color:blue'>采集完成，等待抽取</div>";
        }else if($values['status']=='3'){
			echo "<div style='color:red'>正在抽取</div>";
        }else if($values['status']=='4'){
			echo "<div style='color:red'>抽取结束</div>";
        }else if($values['status']=='0'){
			echo "<div style='color:red'>等待采集</div>";
        }
        ?>
        </div>
        </td>
        <td>
        <div id="d" class="b2r" align="center">
        <?php
			if($values['status2']=='1'){
			echo "<div style='color:blue'>正在采集</div>";
			}else if($values['status2']=='2'){
				echo "<div style='color:blue'>采集完成，等待抽取</div>";
			}else if($values['status2']=='3'){
				echo "<div style='color:red'>正在抽取</div>";
			}else if($values['status2']=='4'){
				echo "<div style='color:red'>抽取结束</div>";
			}else if($values['status2']=='0'){
				echo "<div style='color:red'>等待采集</div>";
			}
        ?>
        </div>
        </td>
      </tr>
      <?php
  		}	
		?>
        
    </table>
    <div> </div>
  </div>
  <!--翻页 start-->
  <div style="padding-top:15px;">
  共[<B><?=$total?></B>]条记录 共[<?=$PageCount?>]页 <?php /*?>当前是[<?=(($Page-1)*$pagesize+1)?>-<?php echo $Page*$pagesize>$total?$total:$Page*$pagesize;?>]条<?php */?>
    <?php
			if($Page>1) 
				echo "[<a href='list.php?page=".($Page-1)."&viewkey=".$_GET["viewkey"]."'><span>前一页</span></a>]";
			else 
				echo "[<span style='color:grey'>前一页</span>]";
			?>
    <?php
			if($Page<$PageCount) 
				echo "[<a href='list.php?page=".($Page+1)."&viewkey=".$_GET["viewkey"]."'><span>后一页</span></a>]";
			else 
				echo "[<span style='color:grey'>后一页</span>]";
			?>
    <SELECT id="page" onChange="location.href='list.php?page='+document.getElementById('page').value+'&viewkey=<?=$_GET["viewkey"]?>';">
      <?php
			for($i=1;$i<=$PageCount;$i++)
			{
				if($Page==$i) echo "<option selected='selected' value='".$i."'>第".$i."页</option>";
				else echo "<option value='".$i."'>第".$i."页</option>";;
			}
			?>
    </SELECT>
  </div>
  <!--翻页 end-> 
</div>
<!-- foot start-->
    <div class="botCenter">
      <a href="http://ir.fzu.edu.cn/" target="_blank" style="text-decoration:none; color:#666">
      <div style = "margin-right: 50px;margin-top:15px; font-size:14px;">&copy;2016-2017 福州大学信息检索课题组<script type="text/javascript">var cnzz_protocol = (("https:" == document.location.protocol) ? " https://" : " http://");document.write(unescape("%3Cspan id='cnzz_stat_icon_1260761465'%3E%3C/span%3E%3Cscript src='" + cnzz_protocol + "s11.cnzz.com/z_stat.php%3Fid%3D1260761465%26show%3Dpic' type='text/javascript'%3E%3C/script%3E"));</script></div>
      </a>
    </div>
<!-- foot end-->
