<?
session_start();
header("Expires: Mon, 26 Jul 1970 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");      
header("Pragma: no-cache");
header("Content-type: text/html; charset=utf-8");
include ("../include/address.mem.php");
echo "<script>if(self == top) parent.location='".BROWSER_IP."'\n;</script>";
require ("../include/config.inc.php");
$uid=$_REQUEST['uid'];
$langx=$_REQUEST["langx"];
$lv=$_REQUEST["lv"];
$loginname=$_SESSION["loginname"];
require ("../include/traditional.$langx.inc.php");
$sql = "select * from web_system_data where Oid='$uid' and LoginName='$loginname'";
$result = mysql_db_query($dbname,$sql);
$cou=mysql_num_rows($result);
if($cou==0){
	echo "<script>window.open('".BROWSER_IP."','_top')</script>";
	exit;
}

$page=$_REQUEST['page'];
if ($page==''){
	$page=0;
}
$search=$_REQUEST['search'];
if ($search!=''){
    $num=1024;
    $search="and (a.UserName like '%$search%' or a.AddDate like '%$search%' or a.Alias like '%$search%')";
}else{
    $num=100;
}

$sql="SELECT a.ID,a.UserName, a.PassWord, a.Money, a.Alias, a.AddDate, SUM( IF( b.Type =  'S', b.Gold, 0 ) ) AS Money800, SUM( IF( b.Type =  'T', b.Gold, 0 ) ) AS Credit800 FROM web_member_data a, web_sys800_data b WHERE a.Pay_Type=1 and b.Cancel=0 $search and a.UserName = b.UserName GROUP BY a.UserName having Money800<>0 or Credit800<>0";
$result = mysql_db_query($dbname, $sql);
$cou=mysql_num_rows($result);
$page_size=$num;
$page_count=ceil($cou/$page_size);
$offset=$page*$page_size;
$mysql=$sql."  limit $offset,$page_size;";
$result = mysql_db_query($dbname, $mysql);
?>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="/style/agents/control_main.css" type="text/css">
<script src="/js/agents/jquery.js" type="text/javascript"></script>
<script language="javascript">
function onLoad(){
  var obj_page = document.getElementById('page');
  obj_page.value = '<?=$page?>';
}
function sbar(st){
st.style.backgroundColor='#BFDFFF';
}
function cbar(st){
st.style.backgroundColor='';
}
</script>
<script language="javascript">
function posts(id,value){
	$.ajax({
   type: "POST",
   url: "moneyfixed.php",
   data: "id="+id+"&value="+value,
   success: function(msg){
     if(msg==1){ 
	 	alert('提交成功');
		window.location.reload();
	 }else{ 
	 	alert('提交失败');
	 }
   }
});
}
</script>
</head>
<body bgcolor="#FFFFFF" text="#000000" leftmargin="0" topmargin="0" vlink="#0000FF" alink="#0000FF" >
<?
include ("top.php");
?>
<table width="975" border="0" cellpadding="2" cellspacing="1" class="m_tab">

	<tr class="m_title">
	  <td colspan="10">现金额度检查</td>
      <td><a href="checkmoney_backup.php?uid=<?=$uid?>&langx=<?=$langx?>&page=<?=$page?>">现金额度下载</a></td>
  </tr>
	<tr class="m_title">
	<form id="myFORM" ACTION="" METHOD=POST name="FrmData">
	  <td colspan="10">关键字查找:
    <input type=TEXT name="search" size=10 value="" maxlength=20 class="za_text">
    <input type=SUBMIT name="SUBMIT" value="确认" class="za_button"></td>
	  <td>    
	  <select name='page' onChange="self.myFORM.submit()">
<?
if ($page_count==0){
    $page_count=1;
	}
	for($i=0;$i<$page_count;$i++){
		if ($i==$page){
			echo "<option selected value='$i'>".($i+1)."</option>";
		}else{
			echo "<option value='$i'>".($i+1)."</option>";
		}
	}
?>  
  </select> 共<?=$page_count?> 页</td>
	</form>
	</tr>
	<tr class="m_title">
	  <td width="40">序号</td>
	  <td width="90">帐号密码</td>
      <td width="114">开户日期</td>
	  <td width="80">存款总数</td>
	  <td width="80">取款总数</td>
	  <td width="80">总帐输/赢</td>
	  <td width="80">未结算金额</td>
	  <td width="100">有效注額總數</td>
	  <td width="80">目前额度</td>
	  <td width="80">误差</td>
	  <td width="95">功能</td>
	</tr>
<?
$i=1;
while ($row = mysql_fetch_array($result)){
$name=$row['UserName'];
$sq="SELECT M_Name,SUM( IF(M_Result='',BetScore,0)) AS BetScore, SUM(VGOLD) AS VGOLD,SUM(M_Result) AS M_Result FROM web_report_data WHERE ID >0 and M_Name='$name' GROUP BY M_Name";	
$qu=mysql_query($sq);
$ro=mysql_fetch_array($qu);
?>
  <tr class="m_cen" onmouseover=sbar(this) onmouseout=cbar(this)> 
    <td align="center"><?=$i?></td>
    <td align="center"><font color=red><?=$row['UserName']?></font><br><span STYLE='background-color: Yellow;'><?=$row['PassWord']?></span></td>
    <td align="center"><?=$row['AddDate']?></td>
	<td align="right"><?=number_format($row['Money800'],2)?></td>
    <td align="right"><?=number_format($row['Credit800'],2)?></td>
    <td align="right"><?=number_format($ro['M_Result'],2)?></td>
    <td align="right"><?=number_format($ro['BetScore'],2)?></td>
    <td align="right"><?=number_format($ro['VGOLD'],2)?></td>
    <td align="right"><?=number_format($row['Money'],2)?></td>
    <td align="right"><?=number_format($row['Money800']+$row['Credit800']+$ro['M_Result']-$ro['BetScore']-$row['Money'],2)?></td>
    <td align="center"><input type="submit" value="修正"  name="Submit" class="za_button" onClick="posts('<?=$row['ID']?>','<?=$row['Money800']+$row['Credit800']+$ro['M_Result']-$ro['BetScore']-$row['Money']?>')"></td>
<?
$i=$i+1;
}
?>
  </tr>
</table>
</body>
</html>