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
	
	var mysearch=decodeURI(GetQueryString("search"));
	if(mysearch !=null && mysearch.length>0 && mysearch!='null'){
		$("#search").val(mysearch);
	}
	
	function GetQueryString(name){
     var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
     var r = window.location.search.substr(1).match(reg);
     if(r!=null)return  unescape(r[2]); return null;
	}	
	
	$("#addconf").click(function(){	
		//alert($('.configDiv').attr('style'));
		if($('.configDiv').attr('style')!=null){
			$('.configDiv').css('display',"");
			$('#addconf').text("取消配置");
		}else{
			$('.configDiv').css('display',"none");
			$('#addconf').text("添加配置");
		}
		//alert($('.configDiv').attr('style'));
	});
	
	$("#submit").click(function(){
	  var confname=$("#confname").val();
	  var lowprice=$("#lowprice").val();
	  var status=$("#status").val();
	  if(confname==''){
	  	alert('请填写配置名');
		return ;
	  }
	  if(lowprice==''){
	  	alert('请填写最低价');
		return ;
	  }
	  if(status==404){
	  	alert("请选择是否采集变体！");
		return ;
	  }
	  if(isNaN(lowprice)){
   		alert("最低价请输入一个数字");
		return ;
	  }
	  //alert(confname+" "+lowprice+" "+status);
	  var str;
	  if(status==1)
     	  str="是";
	  else
	      str="否";
	  if (confirm("确定添加\n配置名为："+confname+"\n最低价为："+lowprice+"\n是否采集变体："+str+" \n这条配置吗？")){
		  $.ajax({  
			type: "post",  
			url : "addConfig.php",
			dataType:'json',
			data: 'confname='+confname+'&lowprice='+lowprice+'&status='+status,   
			success: function(json)
			{
			   if(json.success==1)
			   {		
					window.location.href='index.php';
					alert('配置成功');
			   }
			   else
			   {
					alert("mysql error:"+json.error);
					alert('配置失败');
			   }
			}   
			});
		}
	});
	
});

function deleConfirm(name,deleid){
	name=decodeURI(name);
	if (confirm("确定要删除'"+name+"'这条配置吗？")) {
    	window.location.href = "index.php?deleid="+deleid;
	}
};
function startconfig(name,configid){
	name=decodeURI(name);
	if (confirm("确定要启动采集'"+name+"'这条配置吗？\n注意：启动后开始采集，采集过程中不可删除该条配置！")) {
    	window.location.href = "index.php?configid="+configid;
	}
}

</script>
<div id="freeget">
<div class="wp fuck_h mb20">
<div>
<div class="input-group" id="url-input">
  <table width="100%" class="table">
    <caption align="top" style="border: 1px solid #cad9ea;padding: 0 0.5em 0;border-bottom: none;background-color: #EDEDED;line-height: 45px;font-size: 25px;font-weight: 600;text-align:center;color: #2770C0;font-family: microsoft Yahei;">
    <a href="index.php" style="text-decoration:none">亚马逊采集配置 V1.0 Beta</a>
    <div style="font-size:9px">版本更新：2016-11-07 08:00:00</div>
    <div  style="padding-bottom:10px;">
    
    </div>
     <!--添加配置 start-->
    <button type="button" id="addconf" style="text-align:right; margin-bottom:10px;">添加配置</button>
    <!--添加配置 end-->
    </caption>
    <?php
        	include_once('./mysql.php');
			$conndb=new ConnDB();
			
			//获取消息总数
			$sql="select count(*) from t_crawl_configuration";
			if(isset($_GET["search"])){//存在搜索
				$search = urldecode($_GET["search"]);
				$sql="select count(*) from t_crawl_configuration where status=1 and 标题 like '%".$search."%'";
			}
			$arr=$conndb->queryarr($sql);
			$total=$arr[0][0];
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
			$sql="select * from t_crawl_configuration  order by create_time desc limit $currentRow, $pagesize";
			//echo $sql;
			
			$request=$conndb->queryarr($sql);
			//var_dump($request);
			
			if(isset($_GET['deleid'])){
				$deleid=$_GET['deleid'];
				$sql="update t_crawl_configuration set configstatus=0 where id=".$deleid;
				$conndb->query($sql);
				echo "<script>window.location.href=document.referrer;</script>";
			}
            
            if(isset($_GET['configid'])){
				$configid=$_GET['configid'];
				$sql="update t_crawl_configuration set configstatus=2 where id=".$configid;
				$conndb->query($sql);
				echo "<script>window.location.href=document.referrer;</script>";
            }
		?>
    <tr>
      <th width="616" style="width: 5%;"><div align="center">ID</div></th>
      <th width="182" style="width: 15%;"><div align="center">配置名</div></th>
      <th width="135" style="width: 5%;"><div align="center">最低价格</div></th>
       <th width="135" style="width: 5%;"><div align="center">是否启动</div></th>
      <th width="135" style="width: 15%;"><div align="center">配置时间</div></th>
      <th width="135" style="width: 5%;"><div align="center">是否采集变体</div></th>
      <th width="135" style="width: 10%;"><div align="center">操作</div></th>
      <th width="135" style="width: 5%;"><div align="center">下载</div></th>
      <th width="135" style="width: 10%;"><div align="center">当前状态</div></th>
      <th width="135" style="width: 5%;"><div align="center">删除</div></th>
    </tr>
    
    <div >
     <tr id="del_49861" style="display:none;" class="configDiv">
	     <td></td>
         <td><input type="text" style="width:100%" id="confname"/></td>
         <td><input type="text" style="width:100%" id="lowprice"/></td>
         <td align="center"><b style="color:#F00">是否采集变体：</b><select id="status">
         					   <option value="404">选择</option>
                               <option value="0">否</option>
                               <option value="1">是</option>
                       </select></td>
         <td align="center"><input type="button" style="width:60%" value="添加" id="submit"/></td>
     </tr>
    </div>
    
    <?php     
			foreach($request as $key => $values){
				if($values['configstatus']==0){
					continue;
				}
		?>
    <tr id="del_49861">
      <td align="center"><?=$values['id']?></td>
      <td align="center"><a id="e_vid" title="<?=$values['name']?>" href="list.php?viewkey=<?=$values['id']?>&title=<?=$values['name']?>" style="text-decoration:none; color:#666">
        <?=$values['name']?>
        </a></td>
      <td align="center"><?=$values['lowest_price']?></td>
      
      <td align="center"><?php 
	      if($values['configstatus']!=2) 
		  	echo "<a href='javascript:void(0);' onClick=\"startconfig('".urlencode($values['name'])."',".$values['id'].")\" style='color:red'><b>点击启动</b></a>"; 
		  else 
		  	echo "<div style='color:#00DB00'>已启动</div>";?>
      </td>
      
      <td align="center"><?=$values['create_time']?></td>
   
       <td align="center"><?php if($values['ischange']==1) echo "<div style='color:red'>是</div>";else echo "<div style='color:#A0522D'>否</div>";?></td>
      <td align="center"><div id="b" class="b2r" align="center"> <a id="e_vid" title="查看详情" href="list.php?viewkey=<?=$values['id']?>&title=<?=$values['name']?>" style="color:#0e90d2;overflow:hidden;width:120px;height:20px;text-indent:-9999px;">查看详情</a> </div></td>
      <!--搜索下载 start-->
      <td align="center"><?php
	  
	  $sql="select count(*) from t_crawl_url where crawl_id=".$values['id'];
	  $request=$conndb->queryarr($sql);
	  $crawlIDNum=$request[0][0];
	 // echo $crawlIDNum;
	  $sql="select count(*) from t_crawl_url where crawl_id=".$values['id']." and status=4";
	  $request=$conndb->queryarr($sql);
	  $IDSucessNum=$request[0][0];
	  
	  $sql="select count(*) from t_crawl_url where crawl_id=".$values['id']." and status2=4";
      $request=$conndb->queryarr($sql);
	  $IDSucessNum2=$request[0][0];
	  
	 // echo $IDSucessNum;
	 if($crawlIDNum>0){
		 if($values['ischange']==1){//配置有变体
			 if($IDSucessNum<$crawlIDNum&&$IDSucessNum2<$crawlIDNum){
				 $CrawlStatus=0;//任务进行中
			 }else if($IDSucessNum==$crawlIDNum&&$IDSucessNum2<$crawlIDNum){
				 $CrawlStatus=1;//无变体完成，有变体进行中
			 }else if($IDSucessNum==$crawlIDNum&&$IDSucessNum2==$crawlIDNum){
				 $CrawlStatus=2;//任务完成
			 }
		 }else{
			 if($IDSucessNum<$crawlIDNum){
				 $CrawlStatus=0;//任务进行中
			 }else if($IDSucessNum==$crawlIDNum){
				 $CrawlStatus=2;//任务完成
			 }
		 }
	 }else{
		 $sql="select count(*) from t_crawl_url_temp where crawl_id=".$values['id'];
	     $request=$conndb->queryarr($sql);
	     $crawlIDNum=$request[0][0];
		 if($crawlIDNum>0)
		 	 $CrawlStatus=4;//等待任务开始
		  else 
    		 $CrawlStatus=3;//未配置url的状态
	 }
	 
	 
	  if($crawlIDNum==$IDSucessNum&&$crawlIDNum>0){		
	  	echo "<a style='color:blue' href='download.php?id=".$values['id']."&name=".$values['name']."&price=".$values['lowest_price']."'  target=_blank>下载</a>";
		}
	  else{
	    echo "<a style='color:grey; cursor:pointer;' title='正在处理中，点击详情查看进度。'>下载</a>";
	  }
	  
	  ?></td>
       <!--搜索下载 end-->
       
        <!--变体下载 start-->
        <td align="center"><?php
		
		  if($CrawlStatus==0)
		  	echo "<div style='color:#F4A460'>任务进行中</div>";
		  else if($CrawlStatus==1)
		  	echo "<div style='color:#CD0000'>单体完成，变体进行中</div>";
		  else if($CrawlStatus==2)
		  	echo "<div style='color:red'>任务完成</div>";
		  else if($CrawlStatus==3)
		  	echo "<div style='color:#A0522D'>无url</div>";
			else if($CrawlStatus==4)
		  	echo "<div style='color:#BF52AD'>等待任务开始</div>";

		  ?></td>
          
        <!--变体下载 end-->
         <td align="center">
         <?php 
		 if($CrawlStatus==0||$CrawlStatus==1)
		 	echo "删除";
		 else{
		 	?>
         <a href="javascript:void(0);" onClick="deleConfirm('<?=urlencode($values['name'])?>',<?=$values['id']?>)">删除</a>
         <?php }?></td>
    </tr>
    <?php
  		}	
		?>
  </table>
  <div> </div>
</div>
<!--翻页 start-->
<div style="padding-top:15px;"> 共[<B><?=$total?></B>]条记录 共[<?=$PageCount?>]页 当前是[<?=(($Page-1)*$pagesize+1)?>-<?php echo $Page*$pagesize>$total?$total:$Page*$pagesize;?>]条
  <?php
			if($Page>1) 
				echo "[<a href='index.php?page=".($Page-1)."'><span>前一页</span></a>]";
			else 
				echo "[<span style='color:grey'>前一页</span>]";
			?>
  <?php
			if($Page<$PageCount) 
				echo "[<a href='index.php?page=".($Page+1)."'><span>后一页</span></a>]";
			else 
				echo "[<span style='color:grey'>后一页</span>]";
			?>
  <SELECT id="page" onChange="location.href='index.php?page='+document.getElementById('page').value;">
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
	
  <div  style = "margin-right: 50px;margin-top:15px; font-size:14px;">
  <a href="http://ir.fzu.edu.cn/" target="_blank" style="text-decoration:none; color:#666">&copy;2016-2017 福州大学信息检索课题组</a><script type="text/javascript">var cnzz_protocol = (("https:" == document.location.protocol) ? " https://" : " http://");document.write(unescape("%3Cspan id='cnzz_stat_icon_1260761465'%3E%3C/span%3E%3Cscript src='" + cnzz_protocol + "s11.cnzz.com/z_stat.php%3Fid%3D1260761465%26show%3Dpic' type='text/javascript'%3E%3C/script%3E"));</script></div>
   </div>
<!-- foot end--> 
