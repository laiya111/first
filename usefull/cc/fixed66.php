<?
ignore_user_abort(1);
set_time_limit (0);
require ("config.inc.php");
$datatime=date('Y-m-d H:i:s');
$dd='2018-04-01';
///////1.备份数据表////////////////////////
mysql_query("create table `web_member_data".$dd."` like web_member_data") or die(mysql_error());
mysql_query("create table `web_sys800_data".$dd."` like web_sys800_data") or die(mysql_error());
mysql_query("create table `web_report_data".$dd."` like web_report_data") or die(mysql_error());
mysql_query("create table `bill_ag_table".$dd."` like bill_ag_table") or die(mysql_error());
mysql_query("create table `bill_gdcp_table".$dd."` like bill_gdcp_table") or die(mysql_error());
mysql_query("create table `bill_gdcp_tranter".$dd."` like bill_gdcp_tranter") or die(mysql_error());
mysql_query("create table `bill_pt_table".$dd."` like bill_pt_table") or die(mysql_error());
mysql_query("create table `bill_mg_table".$dd."` like bill_mg_table") or die(mysql_error());
mysql_query("DROP TABLE IF EXISTS web_report_data01") or die(mysql_error());
mysql_query("create table `web_report_data01` like web_report_data") or die(mysql_error());
mysql_query("insert into `web_member_data".$dd."` select * from web_member_data") or die(mysql_error());
mysql_query("insert into `web_sys800_data".$dd."` select * from web_sys800_data") or die(mysql_error());
mysql_query("insert into `web_report_data".$dd."` select * from web_report_data") or die(mysql_error());
mysql_query("insert into `bill_ag_table".$dd."` select * from bill_ag_table") or die(mysql_error());
mysql_query("insert into `bill_gdcp_table".$dd."` select * from bill_gdcp_table") or die(mysql_error());
mysql_query("insert into `bill_gdcp_tranter".$dd."` select * from bill_gdcp_tranter") or die(mysql_error());
mysql_query("insert into `bill_pt_table".$dd."` select * from bill_pt_table") or die(mysql_error());
mysql_query("insert into `bill_mg_table".$dd."` select * from bill_mg_table") or die(mysql_error());
/////////2.备份money到money2//////////////////
mysql_query("update web_member_data set money2=money ");
/////////3.删除数据//////////////////////////
mysql_query("DELETE FROM `web_sys800_data` WHERE `AddDate`<'$dd'");
mysql_query("DELETE FROM `web_report_data` WHERE `M_Date`<'$dd' and checked=1");
mysql_query("DELETE FROM `web_report_data` WHERE `m_result`='0' and `M_Date`<'$dd'");
mysql_query("DELETE FROM `web_report_data` WHERE `M_Date`<'$dd' and  Active=0 and checked=0 and LineType=0 and BetTime='0000-00-00 00:00:00'");
mysql_query("DELETE FROM `bill_ag_table` WHERE `betTime`<'$dd'");
mysql_query("DELETE FROM `bill_gdcp_table` WHERE `betTime` <'$dd'");
mysql_query("DELETE FROM `bill_gdcp_tranter` WHERE `creationTime` <'{$dd} 00:00:00'");
mysql_query("DELETE FROM `bill_pt_table` WHERE `GAMEDATE` <'$dd 00:00:00'");
mysql_query("DELETE FROM `bill_mg_table` WHERE `transactionTimestampDate` < ".strtotime($dd.' 00:00:00')*1000);
/////////优化删除数据的表//////////////////
mysql_query("OPTIMIZE TABLE `web_sys800_data`");
mysql_query("OPTIMIZE TABLE `web_report_data`");
mysql_query("OPTIMIZE TABLE `bill_ag_table`");
mysql_query("OPTIMIZE TABLE `bill_gdcp_table`");
mysql_query("OPTIMIZE TABLE `bill_gdcp_tranter`");
mysql_query("OPTIMIZE TABLE `bill_pt_table`");
mysql_query("OPTIMIZE TABLE `bill_mg_table`");
mysql_query("OPTIMIZE TABLE `web_member_data`");
//////////////4///////////////////////////
mysql_query("INSERT INTO web_sys800_data  (UserName,Type,Gold,Checked) SELECT UserName,'S',0,'1' FROM web_member_data");
mysql_query("INSERT INTO web_report_data (M_Name,M_Result) SELECT UserName,0 FROM web_member_data");
//////////////5////////////////////////////
$sql="SELECT a.ID,a.UserName, a.Money,a.Money2, SUM( IF( b.Type =  'S', b.Gold, 0 ) ) AS Money800, SUM( IF( b.Type =  'T', b.Gold, 0 ) ) AS Credit800 FROM web_member_data a, web_sys800_data b WHERE a.Pay_Type=1 and b.Checked=1 and b.Cancel=0 and a.UserName = b.UserName GROUP BY a.UserName";
$result = mysql_query($sql);
$i=1;
while ($row = mysql_fetch_assoc($result)){
	$name=$row['UserName'];
	$id=$row['ID'];
	$sq="SELECT M_Name,SUM( IF(M_Result='',BetScore,0)) AS BetScore, SUM(VGOLD) AS VGOLD,SUM(M_Result) AS M_Result FROM web_report_data WHERE ID >0 and M_Name='$name' GROUP BY M_Name";
	$qu=mysql_query($sq);
	$ro=mysql_fetch_assoc($qu);
	$amount=$row['Money2']-($row['Money800']+$row['Credit800']+$ro['M_Result']-$ro['BetScore']);
	//echo $row['UserName']."-kk-".$row['Money2']."ee".$row['Money800']."a".$row['Credit800']."c".$ro['M_Result']."d".$ro['BetScore']."<br>";
	$up="INSERT INTO web_report_data (M_Name,M_Result) VALUES ('$name','$amount')";
	//echo $up."<br>";
	mysql_query($up);
	echo '误差帐号：'.$name.'修正金额：'.abs($amount).'<br>';
$i=$i+1;
}
mysql_query("insert into `web_report_data01` select * from `web_report_data".$dd."`") or die(mysql_error());
///////////////6
//header("location:fixed.php");

?>
<html>
<head>
<title>现金额度误差修正</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="/style/agents/control_down.css" rel="stylesheet" type="text/css">
</head>
<script>
<!--
var limit="3000000000000000"
if (document.images){
	var parselimit=limit
}
function beginrefresh(){
if (!document.images)
	return
if (parselimit==1)
	window.location.reload()
else{
	parselimit-=1
	curmin=Math.floor(parselimit)
	if (curmin!=0)
		curtime=curmin+" 秒后自动更新本页！"
	else
		curtime=cursec+" 秒后自动更新本页！"
		timeinfo.innerText=curtime
		setTimeout("beginrefresh()",1000)
	}
}

window.onload=beginrefresh
</script>
<body>
<table width="300" height="300" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td width="300" height="300" align="center"><br>目前累计有 <?=$i?> 个现金会员<br><br><font color="#FFFFFF"><span style="background-color: #FF0000">现金额度误差修正，请勿关闭窗口...</span></font><br><br><span id="timeinfo"></span><br><br>
      <input type=button name=button value="修正更新" onClick="window.location.reload()"></td>
  </tr>
</table>
</body>
</html>
