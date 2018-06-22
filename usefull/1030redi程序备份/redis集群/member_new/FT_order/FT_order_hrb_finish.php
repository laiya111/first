<?
$frame_rel=2;
$login_out_exit=1;
include ("../include/checklogin.php");
require ("../include/redis.conf.php");
$memrow=$row;
if(empty($_SESSION['token'])){
    if(!empty($_COOKIE['token'])){
        $_SESSION['token'] = $_COOKIE['token'];
    }
}
if ($_SESSION['token'] != $_REQUEST['token']) {
    ?>
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title></title>
        <link rel="stylesheet" href="/style/member/mem_order_ft<?= $css ?>.css?v=20161215 " type="text/css">
    </head>
    <body id="OFIN" class="bodyset" onSelectStart="self.event.returnValue=false"
          oncontextmenu="self.event.returnValue=false;window.event.returnValue=false;">
    <div class="ord ord_top">
        <div class="title"><h1>重复提交</h1></div>
        <div class="main">
            <div class="fin_title">
                <p class="error" style=''>您的注单已提交，可到交易状况查看，请不要重复提交注单谢谢！！</p>
            </div>
        </div>
    </div>
    </body>
    </html>
    <?
    exit;
} else {
    unset($_SESSION['token']);
}
require ("../include/define_function_list.inc.php");
require ("../include/curl_http.php");

$uid=$_REQUEST['uid'];
$langx=$_REQUEST['langx'];
$gid=(int)$_REQUEST['gid'];
$type=$_REQUEST['type'];
$gnum=$_REQUEST['gnum'];
$strong=$_REQUEST['strong'];
$bet_url = $_REQUEST['bet_url'];
$odd_f_type='H';
$ioradio_r_h=$_REQUEST['ioradio_r_h'];
$gold=(int)$_REQUEST['gold'];
$active=$_REQUEST['active'];
$line=$_REQUEST['line_type'];
$restcredit=$_REQUEST['restcredit'];
require ("../include/traditional.$langx.inc.php");


if(1){
$open=$memrow['OpenType'];
$pay_type =$memrow['Pay_Type'];
$memname=$memrow['UserName'];
$agents=$memrow['Agents'];
$world=$memrow['World'];
$corprator=$memrow['Corprator'];
$super=$memrow['Super'];
$admin=$memrow['Admin'];
$w_ratio=$memrow['ratio'];
$HMoney=$memrow['Money'];
if ($HMoney < $gold){
	echo "<script>window.open('".BROWSER_IP."/tpl/logout_warn.html','_top')</script>";
	exit;
}
$w_current=$memrow['CurType'];
$havemoney=$HMoney-$gold;
$memid=$memrow['ID'];

$low_odds=$memrow['low_odds'];

	//mysql_query("insert into log1(log)values('".(json_encode($_REQUEST).json_encode($_COOKIE))."');");
$mysql = "select datasite,uid from web_system_data where id=1";
$result = mysql_db_query($dbname,$mysql);
$row = mysql_fetch_array($result);
$site=$row['datasite'];
$suid=$row['uid'];
	include_once("../include/mul_ip.php");
$curl = &new Curl_HTTP_Client();
$curl->store_cookies("cookies.txt");
$curl->set_user_agent("Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");
$curl->set_referrer("".$site."/app/member/FT_index.php?rtype=re&uid=$suid&langx=zh-cn&mtype=3");
switch ($line){
case '20':
    $html_data=$curl->fetch_url("".$site."/app/member/FT_order/FT_order_hrou.php?gid=$gid&uid=$suid&type=$type&gnum=$gnum&odd_f_type=$odd_f_type");
	break;
case '19':
	$html_data=$curl->fetch_url("".$site."/app/member/FT_order/FT_order_hre.php?gid=$gid&uid=$suid&type=$type&gnum=$gnum&strong=$strong&odd_f_type=$odd_f_type");
	break;
case '31':
	$html_data=$curl->fetch_url("".$site."/app/member/FT_order/FT_order_hrm.php?gid=$gid&uid=$suid&type=$type&gnum=$gnum&odd_f_type=$odd_f_type");
	break;
}
preg_match('/<input.* name="gold"/Usi', $html_data, $m_temp);
if(!$m_temp){
	echo "<script>window.location.href = '/app/member/wager_finish.php?uid=$uid&langx=$langx';</script>";
	exit();
}
$sgid=$gid-1;
$mysql = "select * from `match_sports` where Type='FT' and `MID`='$sgid' and Open=1 and source_type<>'BET' and MB_Team!='' and MB_Team_tw!=''"; //and MB_Team_en!=''
$result = mysql_db_query($dbname,$mysql);
$row = mysql_fetch_array($result);
$cou=mysql_num_rows($result);
//redis缓存overdue赛事封盘
//$redis->select(1);
$redisOverdue = json_decode($redis->get($gid),true);
if ($cou == 0 || $redisOverdue['overdue'] == '0') {
    if ($redisOverdue['overdue'] == '0')
            echo "<script>window.parent.parent.body.body_browse.location.reload();window.location.href = '/app/member/wager_finish.php?uid=$uid&langx=$langx';</script>";
        else
            echo "<script>window.location.href = '/app/member/wager_finish.php?uid=$uid&langx=$langx';</script>";
        exit();
    }
	//主客队伍名称
	$w_tg_team=$row['TG_Team'];
	$w_tg_team_tw=$row['TG_Team_tw'];
	$w_tg_team_en=$row['TG_Team_en'];
	$now_play=$row['now_play'];
	if($now_play){
		$type1=explode('||',$now_play);
		$type2=$type1[1];
	}

	$w_mb_team=$row['MB_Team'];
	$w_mb_team_tw=$row['MB_Team_tw'];
	$w_mb_team_en=$row['MB_Team_en'];

	$w_mb_team=filiter_team(trim($row['MB_Team']));
	$w_tg_team=filiter_team(trim($row['TG_Team']));
	$w_mb_team_tw=filiter_team(trim($row['MB_Team_tw']));
	$w_tg_team_tw=filiter_team(trim($row['TG_Team_tw']));
	$w_mb_team_en=filiter_team(trim($row['MB_Team_en']));
	$w_tg_team_en=filiter_team(trim($row['TG_Team_en']));

	//取出当前字库的主客队伍名称

	$s_mb_team=filiter_team($row[$mb_team]);
	$s_tg_team=filiter_team($row[$tg_team]);

	//下注时间
	$m_date=$row["M_Date"];
	$showtype=$row["ShowTypeHRB"];
	$bettime=date('Y-m-d H:i:s');
	$m_start=strtotime($row['M_Start']);
	$datetime=time();
    if ($datetime-$m_start<120){
	    echo "<script>window.location.href = '/app/member/wager_finish.php?uid=$uid&langx=$langx';</script>";
	    exit();
	}
	//联盟
	if ($row[$m_sleague]==''){
		$w_sleague=$row['M_League'];
		$w_sleague_tw=$row['M_League_tw'];
		$w_sleague_en=$row['M_League_en'];
		$s_sleague=$row[$m_league];
	}

	$inball=$row['MB_Ball'].":".$row['TG_Ball'];
	$inball1=$inball;
	$mb_ball = $row['MB_Ball'];
	$tg_ball = $row['TG_Ball'];
	switch ($line){
	case 31:
	  	$bet_type='半场滚球独赢';
        $bet_type_new = '(滚球)&nbsp;独赢&nbsp;-&nbsp;半场';
		$bet_type_tw="半場滾球獨贏";
		$bet_type_en="1st Half Running 1x2";
		$btype="-&nbsp;<font color=red><b>[$Order_1st_Half]</b></font>";
		$caption=$Order_FT.$Order_1st_Half_Running_1_x_2_betting_order;
		$turn_rate="FT_Turn_M";
		$turn="FT_Turn_M";
		$MB_Rate=num_rate($open,$row["MB_Win_Rate_RB_H"]);
		$TG_Rate=num_rate($open,$row["TG_Win_Rate_RB_H"]);
		switch ($type){
		case "H":
			$w_m_place=$w_mb_team;
			$w_m_place_tw=$w_mb_team_tw;
			$w_m_place_en=$w_mb_team_en;
			$s_m_place=$row[$mb_team];
			$w_m_rate=num_rate($open,$row["MB_Win_Rate_RB_H"]);
			$turn_url="/app/member/FT_order/FT_order_hrm.php?gid=".$gid."&uid=".$uid."&type=".$type."&gnum=".$gnum."&odd_f_type=".$odd_f_type;
			$w_gtype='VRMH';
			break;
		case "C":
			$w_m_place=$w_tg_team;
			$w_m_place_tw=$w_tg_team_tw;
			$w_m_place_en=$w_tg_team_en;
			$s_m_place=$row[$tg_team];
			$w_m_rate=num_rate($open,$row["TG_Win_Rate_RB_H"]);
			$turn_url="/app/member/FT_order/FT_order_hrm.php?gid=".$gid."&uid=".$uid."&type=".$type."&gnum=".$gnum."&odd_f_type=".$odd_f_type;
			$w_gtype='VRMC';
			break;
		case "N":
			$w_m_place="和局";
			$w_m_place_tw="和局";
			$w_m_place_en="Flat";
			$s_m_place=$Draw;
			$w_m_rate=num_rate($open,$row["M_Flat_Rate_RB_H"]);
			$turn_url="/app/member/FT_order/FT_order_hrm.php?gid=".$gid."&uid=".$uid."&type=".$type."&gnum=".$gnum."&odd_f_type=".$odd_f_type;
			$w_gtype='VRMN';
			break;
		}
		$Sign="VS.";
		$grape=$type;
		$gwin=($w_m_rate-1)*$gold;
		$ptype='VRM';
		break;
	case 19:
 		$bet_type='半场滚球让球';
        $bet_type_new = '(滚球)&nbsp;让球&nbsp;-&nbsp;半场';
		$bet_type_tw="半場滾球讓球";
		$bet_type_en="1st Half Running Ball";
		$btype="-<font color=red><b>[$Order_1st_Half]</b></font>";
		$caption=$Order_FT.$Order_1st_Half_Running_Ball_betting_order;
		$turn_rate="FT_Turn_RE_".$open;
		$MB_LetB_Rate_RB_H=change_rate($open,$row["MB_LetB_Rate_RB_H"]);
		$TG_LetB_Rate_RB_H=change_rate($open,$row["TG_LetB_Rate_RB_H"]);
		// $rate=get_other_ioratio($odd_f_type,$MB_LetB_Rate_RB_H,$TG_LetB_Rate_RB_H,100);
		$MB_Rate=change_rate($open,$row["MB_LetB_Rate_RB_H"]);
		$TG_Rate=change_rate($open,$row["TG_LetB_Rate_RB_H"]);
		switch ($type){
		case "H":
			$w_m_place=$w_mb_team;
			$w_m_place_tw=$w_mb_team_tw;
			$w_m_place_en=$w_mb_team_en;
			$s_m_place=$s_mb_team;
			// $w_m_rate=number_format($rate[0],3);
			$w_m_rate = $MB_Rate;
			$turn_url="/app/member/FT_order/FT_order_hre.php?gid=".$gid."&uid=".$uid."&type=".$type."&gnum=".$gnum."&strong=".$strong."&odd_f_type=".$odd_f_type;
			$w_gtype='VRRH';
			break;
		case "C":
			$w_m_place=$w_tg_team;
			$w_m_place_tw=$w_tg_team_tw;
			$w_m_place_en=$w_tg_team_en;
			$s_m_place=$s_tg_team;
			// $w_m_rate=number_format($rate[1],3);
			$w_m_rate = $TG_Rate;
			$turn_url="/app/member/FT_order/FT_order_hre.php?gid=".$gid."&uid=".$uid."&type=".$type."&gnum=".$gnum."&strong=".$strong."&odd_f_type=".$odd_f_type;
			$w_gtype='VRRC';
			break;
		}

		$Sign=$row['M_LetB_RB_H'];
		$grape=$Sign;
		if (strtoupper($showtype)=="H"){
			$l_team=$s_mb_team;
			$r_team=$s_tg_team;
			$w_l_team=$w_mb_team;
			$w_l_team_tw=$w_mb_team_tw;
			$w_l_team_en=$w_mb_team_en;
			$w_r_team=$w_tg_team;
			$w_r_team_tw=$w_tg_team_tw;
			$w_r_team_en=$w_tg_team_en;
			$inball=$row['MB_Ball'].":".$row['TG_Ball'];
		}else{
			$r_team=$s_mb_team;
			$l_team=$s_tg_team;
			$w_r_team=$w_mb_team;
			$w_r_team_tw=$w_mb_team_tw;
			$w_r_team_en=$w_mb_team_en;
			$w_l_team=$w_tg_team;
			$w_l_team_tw=$w_tg_team_tw;
			$w_l_team_en=$w_tg_team_en;
			$inball=$row['TG_Ball'].":".$row['MB_Ball'];
			$tem_rate = $MB_Rate;
			$MB_Rate = $TG_Rate;
			$TG_Rate = $tem_rate;
		}
		$s_mb_team=$l_team;
		$s_tg_team=$r_team;
		$w_mb_team=$w_l_team;
		$w_mb_team_tw=$w_l_team_tw;
		$w_mb_team_en=$w_l_team_en;
		$w_tg_team=$w_r_team;
		$w_tg_team_tw=$w_r_team_tw;
		$w_tg_team_en=$w_r_team_en;
		$turn="FT_Turn_RE";
		if ($odd_f_type=='H'){
		    $gwin=($w_m_rate)*$gold;
		}else if ($odd_f_type=='M' or $odd_f_type=='I'){
		    if ($w_m_rate<0){
				$gwin=$gold;
			}else{
				$gwin=($w_m_rate)*$gold;
			}
		}else if ($odd_f_type=='E'){
		    $gwin=($w_m_rate-1)*$gold;
		}
		$ptype='VRE';
		break;
	case 20:
		$bet_type='半场滚球大小';
        $bet_type_new = '(滚球)&nbsp;大&nbsp;/&nbsp;小';
		$bet_type_tw="半場滾球大小";
		$bet_type_en="1st Half Running Over/Under";
		$btype="- <font color=red><b>[$Order_1st_Half]</b></font>";
		$caption=$Order_FT.$Order_1st_Half_Running_Ball_Over_Under_betting_order;
		$turn_rate="FT_Turn_OU_".$open;
		$MB_Dime_Rate_RB_H=change_rate($open,$row["MB_Dime_Rate_RB_H"]);
		$TG_Dime_Rate_RB_H=change_rate($open,$row["TG_Dime_Rate_RB_H"]);
		$rate=get_other_ioratio($odd_f_type,$MB_Dime_Rate_RB_H,$TG_Dime_Rate_RB_H,100);
		$MB_Rate=change_rate($open,$rate[0]);
		$TG_Rate=change_rate($open,$rate[1]);
		switch ($type){
		case "C":
			$w_m_place=$row["MB_Dime_RB_H"];
			$w_m_place=str_replace('O','大&nbsp;',$w_m_place);
			$w_m_place_tw=$row["MB_Dime_RB_H"];
			$w_m_place_tw=str_replace('O','大&nbsp;',$w_m_place_tw);
			$w_m_place_en=$row["MB_Dime_RB_H"];
			$w_m_place_en=str_replace('O','over&nbsp;',$w_m_place_en);
			$m_place=$row["MB_Dime_RB_H"];
			$s_m_place=$row["MB_Dime_RB_H"];
			if ($langx=="zh-cn"){
	            $s_m_place=str_replace('O','大&nbsp;',$s_m_place);
		    }else if ($langx=="zh-tw"){
		        $s_m_place=str_replace('O','大&nbsp;',$s_m_place);
		    }else if ($langx=="en-us" or $langx=='th-tis'){
		        $s_m_place=str_replace('O','over&nbsp;',$s_m_place);
			}

			$peiqiu=str_replace('O','',$row["MB_Dime_RB_H"]);
			$peiqius=explode('/',$peiqiu);
			$min=$peiqius[0]+0;
			if(($mb_ball+$tg_ball)>$min){
				echo "<script>window.location.href = '/app/member/wager_finish.php?uid=$uid&langx=$langx';</script>";
				exit;
			}
			$w_m_rate=number_format($rate[0],3);
			$turn_url="/app/member/FT_order/FT_order_hrou.php?gid=".$gid."&uid=".$uid."&type=".$type."&gnum=".$gnum."&odd_f_type=".$odd_f_type;
			$w_gtype='VROUH';
			break;
		case "H":
			$w_m_place=$row["TG_Dime_RB_H"];
			$w_m_place=str_replace('U','小&nbsp;',$w_m_place);
			$w_m_place_tw=$row["TG_Dime_RB_H"];
			$w_m_place_tw=str_replace('U','小&nbsp;',$w_m_place_tw);
			$w_m_place_en=$row["TG_Dime_RB_H"];
			$w_m_place_en=str_replace('U','under&nbsp;',$w_m_place_en);
			$m_place=$row["TG_Dime_RB_H"];
			$s_m_place=$row["TG_Dime_RB_H"];
			if ($langx=="zh-cn"){
	            $s_m_place=str_replace('U','小&nbsp;',$s_m_place);
		    }else if ($langx=="zh-tw"){
		        $s_m_place=str_replace('U','小&nbsp;',$s_m_place);
		    }else if ($langx=="en-us" or $langx=='th-tis'){
		        $s_m_place=str_replace('U','under&nbsp;',$s_m_place);
			}
			$w_m_rate=number_format($rate[1],3);
			$turn_url="/app/member/FT_order/FT_order_hrou.php?gid=".$gid."&uid=".$uid."&type=".$type."&gnum=".$gnum."&odd_f_type=".$odd_f_type;
			$w_gtype='VROUC';
			break;
		}

		$Sign="VS.";
		$grape=$m_place;
		$turn="FT_Turn_OU";
		if ($odd_f_type=='H'){
		    $gwin=($w_m_rate)*$gold;
		}else if ($odd_f_type=='M' or $odd_f_type=='I'){
		    if ($w_m_rate<0){
				$gwin=$gold;
			}else{
				$gwin=($w_m_rate)*$gold;
			}
		}else if ($odd_f_type=='E'){
		    $gwin=($w_m_rate-1)*$gold;
		}
		$ptype='VROU';
		break;
	}

	if ($gold<10){
		echo "<script>window.location.href = '/app/member/wager_finish.php?uid=$uid&langx=$langx';</script>";
		exit;
	}

	if ($w_m_rate=='' or $grape==''){
		echo "<script>window.location.href = '/app/member/wager_finish.php?uid=$uid&langx=$langx';</script>";
		exit;
	}
	if ($w_m_rate!=$ioradio_r_h){
		$turn_url=$turn_url.'&error_flag=1&langx=' . $langx;
		echo "<script language='javascript'>self.location='$turn_url';</script>";
		exit;
	}
	if ($s_m_place=='' or $w_m_place=='' or $w_m_place_tw==''){
		echo "<script>window.location.href = '/app/member/wager_finish.php?uid=$uid&langx=$langx';</script>";
		exit;
	}
	if ($line==19 or $line==20){
		$oddstype=$odd_f_type;
	}else{
		$oddstype='';
	}

	$bottom1="&nbsp;-&nbsp;<font color=#666666>[上半]</font>";
	$bottom1_tw="&nbsp;-&nbsp;<font color=#666666>[上半]</font>";
	$bottom1_en="&nbsp;-&nbsp;</font><font color=#666666>[1st Half]</font>";

	$w_mb_mid=$row['MB_MID'];
	$w_tg_mid=$row['TG_MID'];

	$lines=$row['M_League']."<br>[".$row['MB_MID'].']vs['.$row['TG_MID']."]<br>".$w_mb_team."&nbsp;<FONT COLOR=#cc0000><b>[".$MB_Rate."]</b></FONT>"."&nbsp;&nbsp;<FONT COLOR=#0000BB><b>".$Sign."</b></FONT>&nbsp;&nbsp;".$w_tg_team."&nbsp;<FONT COLOR=#cc0000><b>[". $TG_Rate."]</b></FONT>"."&nbsp;&nbsp;<FONT color=red><b>$inball</b></FONT><br>";
	$lines=$lines."<FONT color=#cc0000>$w_m_place</FONT>".$bottom1."&nbsp;@&nbsp;<FONT color=#cc0000><b>".$w_m_rate."</b></FONT>";

	$lines_tw=$row['M_League_tw']."<br>[".$row['MB_MID'].']vs['.$row['TG_MID']."]<br>".$w_mb_team_tw."&nbsp;<FONT COLOR=#cc0000><b>[".$MB_Rate."]</b></FONT>"."&nbsp;&nbsp;<FONT COLOR=#0000BB><b>".$Sign."</b></FONT>&nbsp;&nbsp;".$w_tg_team_tw."&nbsp;<FONT COLOR=#cc0000><b>[". $TG_Rate."]</b></FONT>"."&nbsp;&nbsp;<FONT color=red><b>$inball</b></FONT><br>";
	$lines_tw=$lines_tw."<FONT color=#cc0000>$w_m_place_tw</FONT>".$bottom1_tw."&nbsp;@&nbsp;<FONT color=#cc0000><b>".$w_m_rate."</b></FONT>";

	$lines_en=$row['M_League_en']."<br>[".$row['MB_MID'].']vs['.$row['TG_MID']."]<br>".$w_mb_team_en."&nbsp;<FONT COLOR=#cc0000><b>[".$MB_Rate."]</b></FONT>"."&nbsp;&nbsp;<FONT COLOR=#0000BB><b>".$Sign."</b></FONT>&nbsp;&nbsp;".$w_tg_team_en."&nbsp;<FONT COLOR=#cc0000><b>[". $TG_Rate."]</b></FONT>"."&nbsp;&nbsp;<FONT color=red><b>$inball</b></FONT><br>";
	$lines_en=$lines_en."<FONT color=#cc0000>$w_m_place_en</FONT>".$bottom1_en."&nbsp;@&nbsp;<FONT color=#cc0000><b>".$w_m_rate."</b></FONT>";

$ip_addr = get_ip();

$m_turn=0;
$a_rate=0;
$b_rate=0;
$c_rate=0;
$d_rate=0;

$a_point=100;
$b_point=0;
$c_point=0;
$d_point=0;

//低赔率不通过
$line_array = array(1, 5, 11, 21, 31);    //1,独赢;5,单双;11,半场独赢
if (in_array($line,$line_array)) {
	if ($w_m_rate < 1 + $low_odds){
		$low_odds_alert = number_format(1+$low_odds,2) ;
		$bet_url.="&islow_odds=1&low_odds=".$low_odds;
		echo "<script>";
		echo "window.location.href='$bet_url'";
		echo "</script>";
		exit();
	}
}else{
	if ($w_m_rate < $low_odds){
		$bet_url.="&islow_odds=1&low_odds=".$low_odds;
		echo "<script>";
		echo "window.location.href='$bet_url'";
		echo "</script>";
		exit();
	}
}

//空白注单
if($m_date=="" or $m_date=="0000-00-00" or $gold==0 or $gold==""){
	echo "<script>window.location.href = '/app/member/wager_finish.php?uid=$uid&langx=$langx';</script>";
	exit();
}

	$sql = "update web_member_data set Money=Money-'$gold' where UserName='$memname' and Money>=$gold";
	$query = mysql_query($sql);
	if(mysql_errno()==0 && mysql_affected_rows()>0){
		$sql = "INSERT INTO web_report_data	(QQ83068506,danger,MID,Active,LineType,Mtype,M_Date,BetTime,BetScore,Middle,Middle_tw,Middle_en,BetType,BetType_tw,BetType_en,M_Place,M_Rate,M_Name,Gwin,TurnRate,OpenType,OddsType,ShowType,Agents,World,Corprator,Super,Admin,A_Rate,B_Rate,C_Rate,D_Rate,A_Point,B_Point,C_Point,D_Point,BetIP,Ptype,Gtype,CurType,Ratio,MB_MID,TG_MID,Pay_Type,Orderby,MB_Ball,TG_Ball,LastBetMoney,type) values ('$inball1','1','$sgid','$active','$line','$w_gtype','$m_date','$bettime','$gold','$lines','$lines_tw','','$bet_type','$bet_type_tw','$bet_type_en','$grape','$w_m_rate','$memname','$gwin','$m_turn','$open','$oddstype','$showtype','$agents','$world','$corprator','$super','$admin','$a_rate','$b_rate','$c_rate','$d_rate','$a_point','$b_point','$c_point','$d_point','$ip_addr','$ptype','FT','$w_current','$w_ratio','$w_mb_mid','$w_tg_mid','$pay_type','$order','$mb_ball','$tg_ball','$HMoney','$type2')";
		mysql_query($sql) or die ("操作失败!");
		$ouid=mysql_insert_id();
	}
//	$sql = "update web_member_data set Money='$havemoney' where UserName='$memname'";
//	mysql_db_query($dbname,$sql) or die ("操作失败!");
	mysql_close();
?>
<html>
<head>
<meta http-equiv='Content-Type' content="text/html; charset=utf-8">
<script language=javascript>
window.setTimeout('sendsubmit()',500);
function sendsubmit(){
}
</script>
<title></title>
<link rel="stylesheet" href="/style/member/mem_order_ft<?=$css?>.css?v=20161215 " type="text/css">


</head>
<script language="JavaScript" src="/js/order_finish.js?v=20161215 "></script>
<body id="OFIN" class="bodyset" onSelectStart="self.event.returnValue=false" oncontextmenu="self.event.returnValue=false;window.event.returnValue=false;">
  <div class="ord ord_new">
    <div class="title"><h1>足球</h1></div>
    <div class="main">
        <div class="fin_title">
        	<p class="fin_acc">下注成功</p>
         	<p class="fin_uid">注单号：<?=show_voucher($line,$ouid)?></p>
         	<p class="error" >危险球 - 待确认</p>
        </div>
	 		<div class="gametype"><?= $bet_type_new ?></div>
            <div class="leag"><?= $s_sleague ?></div>
            <div class="teamName">
                <span class="tName"><?= $s_mb_team ?>&nbsp;&nbsp;<span class="radio">
                <span class="radio"><?= $Sign ?></span></span>&nbsp;<?= $s_tg_team ?></span>
            </div>
            <p class="fin_team"><em><?= $s_m_place ?></em>&nbsp;@&nbsp;<strong><?= $w_m_rate ?></strong></p>

            <div class="betdata ft">
                <p class="fin_amount">交易金额：<span class="fin_gold"><?=$gold?></span></p>
                <p class="mayWin">可赢金额：<font id="pc"><?=$gwin?></font></p>
                <p class="fin_uid">注单号：<span><?=show_voucher($line,$ouid)?></span></p>
                <p class="fin_acc">下注成功</p>
            </div>
        </div>

        <div class="betBox">
            <!-- <input type="button" name="PRINT" value="列印" onClick="window.print()" class="print">  -->
            <input type="button" name="FINISH" value="确认" onClick="parent.close_bet();" class="close">
        </div>
  </div>
</body>
</html><script>
parent.parent.header.reloadCredit('RMB <?=$havemoney?>');
</script>
<?
}
mysql_close();
?>
