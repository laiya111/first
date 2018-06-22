<?
$frame_rel=2;
include ("../include/checklogin.php");

require ("../include/config.slave.php");
require ("../include/define_function_list.inc.php");
require ("../include/curl_http.php");
require ("../include/redis.conf.php");
$uid=$_REQUEST['uid'];
$langx=$_REQUEST['langx'];
$gid=(int)$_REQUEST['gid'];
$type=$_REQUEST['type'];
$gnum=$_REQUEST['gnum'];
$strong=$_REQUEST['strong'];
$token=md5(time().  $_SESSION['username']);
$_SESSION['token']=$token;
setcookie('token',$token, time()+20);
$islow_odds=$_REQUEST['islow_odds'];

$low_odds=$_REQUEST['low_odds'];

$bet_url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

$odd_f_type='H';
$error_flag=$_REQUEST['error_flag'];
require ("../include/traditional.$langx.inc.php");


$memname=$row['UserName'];
$credit=$row['Money'];
$curtype=$row['CurType'];
$pay_type=$row['Pay_Type'];
$btset=singleset('RE');
$GMIN_SINGLE=$btset[0];
$GMAX_SINGLE=$row['FT_RE_Scene'];
$GSINGLE_CREDIT=$row['FT_RE_Bet'];

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
$html_data=$curl->fetch_url("".$site."/app/member/FT_order/FT_order_re.php?gid=$gid&uid=$suid&type=$type&gnum=$gnum&strong=$strong&odd_f_type=$odd_f_type");
preg_match('/<input name="gold"/Usi',$html_data,$m_temp);
if(!$m_temp){
	echo "<script>window.location.href = '/app/member/wager_finish.php?uid=$uid&langx=$langx';</script>";
	exit;
}

preg_match('/<strong class="light" id="ioradio_id">([0-9\.]{1,8})<\/strong>/Usi',$html_data,$rates);
if(empty($rates)|| !is_numeric($rates[1])){
	echo "<script>window.location.href = '/app/member/wager_finish.php?uid=$uid&langx=$langx';</script>";
	exit;
}
else{
	//$uprate='ior_'.$rtype;
	//mysql_query("update match_sports set {$uprate}={$rates[1]} where MID='$gid'") or die("update match_sports set {$uprate}={$rates[1][0]} where MID='$gid'");
}
}*/
$mysql = "select M_Date,M_Time,$mb_team as MB_Team,$tg_team as TG_Team,$m_league as M_League,ShowTypeRB,M_LetB_RB,MB_LetB_Rate_RB,TG_LetB_Rate_RB,MB_Ball,TG_Ball,overdue from `match_sports` where Type='FT' and `MID`=$gid and Cancel!=1 and Open=1 and source_type<>'BET' and $mb_team!=''";
$result = mysql_query($mysql,$master);
$row = mysql_fetch_array($result);
$cou=mysql_num_rows($result);
//redis缓存overdue赛事封盘
//$redis->select(1);
$redisOverdue = json_decode($redis->get($gid),true);
if ($cou == 0 || $redisOverdue['overdue'] == '0') {
    if ($memname == 'cc6669') {
        echo 33;
        exit();
    }
    if ($redisOverdue['overdue'] == '0')
        echo "<script>window.parent.parent.body.body_browse.location.reload();window.location.href = '/app/member/wager_finish.php?uid=$uid&langx=$langx';</script>";
    else
        echo "<script>window.location.href = '/app/member/wager_finish.php?uid=$uid&langx=$langx';</script>";
    exit();
} else{
	$M_League=$row['M_League'];
	$MB_Team=$row["MB_Team"];
	$TG_Team=$row["TG_Team"];
	$MB_Team=filiter_team($MB_Team);
	$Sign=$row['M_LetB_RB'];
	$MB_LetB_Rate_RB=change_rate($open,$row["MB_LetB_Rate_RB"]);
	$TG_LetB_Rate_RB=change_rate($open,$row["TG_LetB_Rate_RB"]);
	$rate=get_other_ioratio($odd_f_type,$MB_LetB_Rate_RB,$TG_LetB_Rate_RB,100);
	switch ($type){
	case "H":
		$M_Place=$MB_Team;
		$M_Rate=number_format($rate[0],2);
		$mtype='RRH';
		break;
	case "C":
		$M_Place=$TG_Team;
		$M_Rate=number_format($rate[1],2);
		$mtype='RRC';
		break;
	}
	$inball=$row['MB_Ball'].":".$row['TG_Ball'];
	if ($row['ShowTypeRB']=='C'){
		$inball=$row['TG_Ball'].":".$row['MB_Ball'];
		$Team=$MB_Team;
		$MB_Team=$TG_Team;
		$TG_Team=$Team;
	}
	$ratio_id=$M_Rate;
	$havesql="select sum(BetScore) as BetScore from web_report_data where M_Name='$memname' and MID='$gid' and LineType=9 and Mtype='$mtype' and Active=1";
	$result = mysql_query($havesql,$slave);
	$haverow = mysql_fetch_array($result);
	$have_bet=$haverow['BetScore']+0;

    $sql = "select * from match_league where  $m_league='$M_League' and Type='FT'";
    $result = mysql_query($sql,$slave);
    $league = mysql_fetch_array($result);
    $mmb_team=explode("[",$row['MB_Team']);
	//如果是特殊会员，则限额取会员表中数据
	$specialMem_sql = "select status from web_special_member where user_name ='$memname'";
	$specialMem_result = mysql_db_query($dbname,$specialMem_sql);
	$specialMem_row = mysql_fetch_assoc($specialMem_result);
	if($specialMem_row['status'] == '1'){
		$GMAX_SINGLE=$GSINGLE_CREDIT;
		$bettop=$GSINGLE_CREDIT;
	}else{
		if ($mmb_team[1]==$Special1){
			$bettop=$league['CS'];
		}else{

			$GMAX_SINGLE=$GSINGLE_CREDIT>$league['RB']?$league['RB']:$GSINGLE_CREDIT;
			$GSINGLE_CREDIT=$GSINGLE_CREDIT>$league['RB']?$league['RB']:$GSINGLE_CREDIT;
			//$bettop=$league['RB'];
			$bettop=$GSINGLE_CREDIT;

		}
	}
    

	if ($M_Rate==0 or $Sign==''){
	    echo "<script>window.location.href = '/app/member/wager_finish.php?uid=$uid&langx=$langx';</script>";
	    exit;
	}

	if ($odd_f_type=='E'){
		$count=1;
	}else{
		$count=0;
	}
	if ($GSINGLE_CREDIT>=500){
	    if ($M_Rate-$count<=1){
		    $num=1;
	    }else if ($M_Rate-$count>1 and $M_Rate-$count<=1.05){
		    $num=0.95;
		}else if ($M_Rate-$count>1.05 and $M_Rate-$count<=1.1){
		    $num=0.9;
		}else if ($M_Rate-$count>1.1 and $M_Rate-$count<=1.15){
		    $num=0.85;
	    }else if ($M_Rate-$count>1.15 and $M_Rate-$count<=1.2){
		    $num=0.8;
		}else if ($M_Rate-$count>1.2 and $M_Rate-$count<=1.25){
		    $num=0.75;
		}else if ($M_Rate-$count>1.25 and $M_Rate-$count<=1.3){
		    $num=0.7;
	    }else if ($M_Rate-$count>1.3 and $M_Rate-$count<=1.35){
		    $num=0.65;
	    }else if ($M_Rate-$count>1.35 and $M_Rate-$count<=1.4){
		    $num=0.6;
		}else if ($M_Rate-$count>1.4 and $M_Rate-$count<=1.45){
		    $num=0.55;
	    }else if ($M_Rate-$count>1.45 and $M_Rate-$count<=1.5){
		    $num=0.5;
		}else if ($M_Rate-$count>1.5){
		    $num=0.45;
	    }
		$number=100;
	}else{
		$num=1;
		$number=1;
	}
	if ($bettop<$GSINGLE_CREDIT){
		$bettop_money=$bettop*$num;
	}else{
		$bettop_money=floor($GSINGLE_CREDIT*$num/$number)*$number;
	}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title></title>
<link rel="stylesheet" href="/style/member/mem_order_ft<?=$css?>.css?v=20161216 " type="text/css">

</head>
<script language="JavaScript" src="/js/football_order<?=$js?>.js?v=20161216 "></script>
<body id="OFT" class="bodyset" onSelectStart="self.event.returnValue=false" oncontextmenu="self.event.returnValue=false;window.event.returnValue=false;">
<form name="LAYOUTFORM" action="/app/member/FT_order/FT_order_rb_finish.php" method="post" onSubmit="return false">
  <div class="ord ord_top">
  	<div class="title"><h1>足球</h1><div class="tiTimer" onClick="orderReload();"><span id="ODtimer">10</span><input type="checkbox" id="checkOrder" onClick="onclickReloadTime()" checked value="10"></div></div>
    <div class="main">

		 <? if($islow_odds==1){?>
	  <p class="error" style=''>您好,本场比赛不接受赔率低于<?=$low_odds?><br>请选择其它赛事投注,谢谢！</p>
	  <? }?>
      <?php
        if ($error_flag==1){
	?>
        <p class="error error_new"><?=$bet_title?></p>
    <?php   
		}
    ?>

      <div class="gametype">(滚球)&nbsp;让球</div>
      <div class="leag"><?=$M_League?></div>
      <div class="teamName"><span class="tName"><?=$MB_Team?>&nbsp;&nbsp;<span class="radio"><span class="radio"><?=$Sign?></span></span>&nbsp;<?=$TG_Team?></span>&nbsp;&nbsp;<em class="bold">(<?=$inball?>)</em>
      <!--span class="vs">vs.</span><span class="tName">布拉加1<br> 塞图巴尔0</span-->
      </div>
      <p class="team"><em><?=$M_Place?></em>&nbsp;&nbsp;@&nbsp;<strong class="light" id="ioradio_id"><?=$M_Rate?></strong></p>
      <p class="auto"><input type="checkbox" id="autoOdd" name="autoOdd" onClick="onclickReloadAutoOdd()" value="Y" ><span class="auto_info" title="在方格里打勾表示，如果投注单里的任何选项在确认投注时赔率变佳，系统会无提示的继续进行该下注。">自动接受较佳赔率</span></p>
      <p class="error" style="display: none;"></p>
      <div class="betdata">
          <p class="amount">交易金额：<input name="gold" type="text" class="txt" id="gold" onKeyPress="return CheckKey(event)" placeholder="投注额" onKeyUp="return CountWinGold()" size="8" maxlength="10"><span id="betClear"></span></p>
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
<input type="hidden" name="strong" value="<?=$strong?>">
<input type="hidden" name="line_type" value="9">
<input type="hidden" name="gid" value="<?=$gid?>">
<input type="hidden" name="type" value="<?=$type?>">
<input type="hidden" name="gnum" value="<?=$gnum?>">
<input type="hidden" name="concede_r" value="1">
<input type="hidden" name="radio_r" value="100">
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
}
mysql_close();
?>
