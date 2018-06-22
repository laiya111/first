<?
$frame_rel=2;
include ("../include/checklogin.php");

require ("../include/config.slave.php");
require ("../include/define_function_list.inc.php");
require ("../include/curl_http.php");
require ("../include/redis.conf.php");
$gid=(int)$_REQUEST['gid'];
$langx=$_REQUEST['langx'];
$uid=$_REQUEST['uid'];
$gnum=$_REQUEST['gnum'];
$type=$_REQUEST['type'];
$token=md5(time().  $_SESSION['username']);
$_SESSION['token']=$token;
setcookie('token',$token, time()+20);
$islow_odds=$_REQUEST['islow_odds'];

$low_odds=$_REQUEST['low_odds'];
$bet_url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

$odd_f_type='H';
$error_flag=$_REQUEST['error_flag'];
require ("../include/traditional.$langx.inc.php");
//////////////////////////////////////////////////

$memname=$row['UserName'];
$credit=$row['Money'];
$curtype=$row['CurType'];
$pay_type=$row['Pay_Type'];
$btset=singleset('M');
$GMIN_SINGLE=$btset[0];
$GMAX_SINGLE=$row['FT_M_Scene'];
$GSINGLE_CREDIT=$row['FT_M_Bet'];
$open=$row['OpenType'];
if ($error_flag==1){
	$bet_title="<tt>".$Order_Odd_changed_please_bet_again."</tt>";
}
////////////////////////////////////////////////////
/*if($credit>=10){
$mysql = "select datasite,uid from web_system_data";
$result = mysql_db_query($dbname,$mysql);
$row = mysql_fetch_array($result);
$site=$row['datasite'];
$suid=$row['uid'];
	include_once("../include/mul_ip.php");
$curl = &new Curl_HTTP_Client();
$curl->store_cookies("cookies.txt");
$curl->set_user_agent("Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");
$curl->set_referrer("".$site."/app/member/FT_index.php?rtype=re&uid=$suid&langx=zh-cn&mtype=3");
$html_data=$curl->fetch_url("".$site."/app/member/FT_order/FT_order_hrm.php?gid=$gid&uid=$suid&type=$type&gnum=$gnum&odd_f_type=$odd_f_type");
preg_match("/足球/Usi",$html_data,$m_temp);
if(!$m_temp){
	echo "<script>window.location.href = '/app/member/wager_finish.php?uid=$uid&langx=$langx';</script>";
	exit();
}}*/
$sgid=$gid-1;
$mysql = "select M_Date,M_Time,$mb_team as MB_Team,$tg_team as TG_Team,$m_league as M_League,MB_Win_Rate_RB_H,TG_Win_Rate_RB_H,M_Flat_Rate_RB_H,MB_Ball,TG_Ball,overdue from `match_sports` where Type='FT' and `MID`='$sgid' and Cancel!=1 and Open=1 and source_type<>'BET' and $mb_team!=''";
$result = mysql_query($mysql,$master);
$row = mysql_fetch_array($result);
$cou=mysql_num_rows($result);
//redis缓存overdue赛事封盘
$redis->select(1);
$redisOverdue = json_decode($redis->get($gid),true);
if ($cou == 0 || $redisOverdue['overdue'] == '0') {
    if ($redisOverdue['overdue'] == '0')
        echo "<script>window.parent.parent.body.body_browse.location.reload();window.location.href = '/app/member/wager_finish.php?uid=$uid&langx=$langx';</script>";
    else
        echo "<script>window.location.href = '/app/member/wager_finish.php?uid=$uid&langx=$langx';</script>";
} else{

	$M_League=$row['M_League'];
	$MB_Team=$row["MB_Team"];
	$TG_Team=$row["TG_Team"];
	$MB_Team=filiter_team($MB_Team);
	$inball=$row['MB_Ball'].":".$row['TG_Ball'];
	switch ($type){
	case "H":
		$M_Place=$MB_Team;
		$M_Rate=num_rate($open,$row["MB_Win_Rate_RB_H"]);
		break;
	case "C":
		$M_Place=$TG_Team;
		$M_Rate=num_rate($open,$row["TG_Win_Rate_RB_H"]);
		break;
	case "N":
		$M_Place=$Draw;
		$M_Rate=num_rate($open,$row["M_Flat_Rate_RB_H"]);
		break;
	}
	$havesql="select sum(BetScore) as BetScore from web_report_data where M_Name='$memname' and MID='$sgid' and LineType=31 and (Active=1 or Active=11)";
	$result = mysql_query($havesql,$slave);
	$haverow = mysql_fetch_array($result);
	$have_bet=$haverow['BetScore']+0;

    $sql = "select * from match_league where  $m_league='$M_League'";
    $result = mysql_query($sql,$slave);
    $league = mysql_fetch_array($result);
	//如果是特殊会员，则限额取会员表中数据
	$specialMem_sql = "select status from web_special_member where user_name ='$memname'";
	$specialMem_result = mysql_db_query($dbname,$specialMem_sql);
	$specialMem_row = mysql_fetch_assoc($specialMem_result);
	if($specialMem_row['status'] == '1'){
		$GMAX_SINGLE=$GSINGLE_CREDIT;
	}else{
		$GMAX_SINGLE=$GSINGLE_CREDIT>$league['VRM']?$league['VRM']:$GSINGLE_CREDIT;
		$GSINGLE_CREDIT=$GSINGLE_CREDIT>$league['VRM']?$league['VRM']:$GSINGLE_CREDIT;
	}
	$bettop=$GSINGLE_CREDIT;

	if ($M_Rate==0){
	    echo "<script>window.location.href = '/app/member/wager_finish.php?uid=$uid&langx=$langx';</script>";
	    exit;
	}

	if ($bettop<$GSINGLE_CREDIT){
		$bettop_money=$bettop;
	}else{
		$bettop_money=$GSINGLE_CREDIT;
	}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title></title>

<link rel="stylesheet" href="/style/member/mem_order_ft<?=$css?>.css?v=20161216 " type="text/css">
<script language="JavaScript" src="/js/football_order_m<?=$js?>.js?v=20161216 "></script>
</head>

<body id="OFT" class="bodyset" onSelectStart="self.event.returnValue=false" oncontextmenu="self.event.returnValue=false;window.event.returnValue=false;">
<form name="LAYOUTFORM" action="/app/member/FT_order/FT_order_hrb_finish.php" method="post" onSubmit="return false">
  <div class="ord ord_top">
    <div class="title"><h1>足球</h1><div class="tiTimer" onClick="orderReload();"><span id="ODtimer">10</span><input type="checkbox" id="checkOrder" onClick="onclickReloadTime()" checked value="10"></div></div>
    <div class="main">

		 <? if($islow_odds==1){?>
	  <p class="error" style=''>本场比赛不接受赔率低于<?=$low_odds?><br>请选择其它赛事投注,谢谢！</p>
	  <? }?>
      <?php
           if ($error_flag==1){
               echo '<p class="error error_new">'.$bet_title.'</p>';
            }
        ?>

      <div class="gametype">(滚球)&nbsp;独赢</div>
      <div class="leag"><?=$M_League?></div>
      <div class="teamName"><span class="tName"><?=$MB_Team?>&nbsp;&nbsp;vs.&nbsp;<?=$TG_Team?></span>&nbsp;&nbsp;<em class="bold">(<?=$inball?>)</em></div>
      <p class="team"><em><?=$M_Place?></em>&nbsp;&nbsp;@&nbsp;<strong class="light" id="ioradio_id"><?=$M_Rate?></strong></p>
      <p class="auto"><input type="checkbox" id="autoOdd" name="autoOdd" onClick="onclickReloadAutoOdd()" value="Y" ><span class="auto_info" title="在方格里打勾表示，如果投注单里的任何选项在确认投注时赔率变佳，系统会无提示的继续进行该下注。">自动接受较佳赔率</span></p>
      <p class="error" style="display: none;"></p>
      <div class="betdata">
          <p class="amount">交易金额：<input name="gold" type="text" class="txt" id="gold" placeholder="投注额" onKeyPress="return CheckKey(event)" onKeyUp="return CountWinGold1()" size="8" maxlength="10"><span id="betClear"></span></p>
          <p class="mayWin"><span class="bet_txt">可赢金额：</span><font id="pc">0</font></p>
          <p class="minBet"><span class="bet_txt">单注最低：</span><?=$GMIN_SINGLE?></p>
          <p class="maxBet"><span class="bet_txt">单注最高：</span><?=number_format($GMAX_SINGLE)?></p>
           <?php include '../betamount.php';?>
    </div>
    </div>
    <div id="gWager" style="display: none;position: absolute;"></div>
    <div id="gbutton" style="display: block;position: absolute;"></div>
    <div class="betBox">
    <input type="button" name="btnCancel" value="取消" onClick="parent.close_bet();" class="no">
    <input type="button" name="Submit" value="确定交易" onClick="return SubChk();" class="yes">
  </div>
  </div>


  <div id="gfoot" style="display: block;position: absolute;"></div>
  <div class="ord" id="line_window" style="display: none;">
    <div class="betChk" id="gdiv_table">
      <span class="notice">*SHOW_STR*</span>
      <input type="button" name="wgCancel" value="取消" onClick="Close_div();" class="no">
      <input type="button" name="wgSubmit" value="确定交易" onmousedown='Sure_wager();' class="yes">
    </div>
  </div>
<input type="hidden" name="uid" value="<?=$uid?>">
<input type="hidden" name="langx" value="<?=$langx?>">
<input type="hidden" name="active" value="1">
<input type="hidden" name="line_type" value="31">
<input type="hidden" name="gid" value="<?=$gid?>">
<input type="hidden" name="type" value="<?=$type?>">
<input type="hidden" name="gnum" value="<?=$gnum?>">
<input type="hidden" name="concede_h" value="1">
<input type="hidden" name="radio_h" value="100">
<input type="hidden" id="ioradio_r_h" name="ioradio_r_h" value="<?=$M_Rate?>">
<input type="hidden" name="gmax_single" value="<?=$bettop_money?>">
<input type="hidden" name="gmin_single" value="<?=$GMIN_SINGLE?>">
<input type="hidden" name="singlecredit" value="<?=$GMAX_SINGLE?>">
<input type="hidden" name="singleorder" value="<?=$GSINGLE_CREDIT?>">
<input type="hidden" name="restsinglecredit" value="<?=$have_bet?>">
<input type="hidden" name="wagerstotal" value="<?=$GMAX_SINGLE?>">
<input type="hidden" name="restcredit" value="<?=$credit?>">
<input type="hidden" name="pay_type" value="<?=$pay_type?>">
<input type="hidden" name="odd_f_type" value="<?=$odd_f_type?>">
<input type="hidden" name="bet_url" value="<?=$bet_url?>">
<input type="hidden" name="token" value="<?=$token?>">
</form>
</body>
<SCRIPT LANGUAGE="JavaScript">document.all.gold.focus();</script>
</html>
<?
mysql_close();
}
?>
