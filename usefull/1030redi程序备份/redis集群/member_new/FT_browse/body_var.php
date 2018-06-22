<?php
// $frame_rel=2;
require '../include/checklogin.php';
require ("../include/define_function_list.inc.php");
require ("../include/curl_http.php");
require ("../include/redis.conf.php");
//include "../include/login_session.php";
function check_score($id,$mb,$tg){
	$sql="select * from match_sports_log where sport_id={$id}";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==0){
		mysql_query("insert into match_sports_log(sport_id,mb,tg) values({$id},'{$mb}','{$tg}')");
	}
	else{
		$arr=mysql_fetch_assoc($res);
		if($mb!=$arr['mb'] || $tg!=$arr['tg']){
			mysql_query("update match_sports_log set log_time=now(),mb='{$mb}',tg='{$tg}' where sport_id={$id}");
		}
	}
}
$uid=$_REQUEST['uid'];
$langx=$_REQUEST['langx'];
if(empty($langx)||$langx=='undefined'){
	$langx='zh-cn';
}
$mtype=$_REQUEST['mtype'];
$rtype=$_REQUEST['rtype'];
$league_id=$_REQUEST['league_id'];
$sort_type=$_REQUEST['sort_type'];
$page_no=$_REQUEST['page_no'];
$showtype=$_REQUEST['showtype'];
require ("../include/traditional.$langx.inc.php");
$open=$row['OpenType'];
$memname=$row['UserName'];
$credit=$row['Money'];

$zbreload = $_REQUEST['zbreload'];
 
if ($league_id=='' and $showtype!='hgft'){
	$num=60;
}else{
	$num=1024;
}
if ($page_no==''){
    $page_no=0;
}
$m_date=date('Y-m-d');
$date=date('m-d');
$K=0;
if($_REQUEST['hot_game']!=''&&$_REQUEST['hot_game']!='undefined'){
	$w2014=" and (MB_Team like '%".$_REQUEST['hot_game']."%' or TG_Team like '%".$_REQUEST['hot_game']."%' or M_League like '%".$_REQUEST['hot_game']."%')";   
}
?>
<html>
<head>
<TITLE>足球變數值</TITLE>
<META http-equiv=Content-Type content="text/html; charset=utf-8">
<script language="javascript" src="/js/thenBy.js"></script>
<SCRIPT language=JavaScript>
parent.flash_ior_set='Y';
parent.minlimit_VAR='0';
parent.maxlimit_VAR='0';
parent.uid='<?=$uid?>';
parent.msg='<?=$mem_msg?>';
parent.ltype='3';
parent.str_even='和';
parent.str_submit='确认';
parent.str_reset='重设';
parent.langx='<?=$langx?>';
parent.rtype='<?=$rtype?>';
parent.sel_lid='<?=$league_id?>';
parent.retime=0;
top.today_gmt='<?=date('Y-m-d')?>';
top.now_gmt='<?=date('H:i:s')?>';
parent.zbreload = '<?=$zbreload?>';
function contains(_ary,_val){
	for(var j=0;j<_ary.length;j++){
		if(_ary[j]==_val){
			return true;
			break;	
		}	
	}
	return false;
}

function trim(str)
{ 
	 return str.replace(/(^\s*)|(\s*$)/g, ""); 
}
		
function re_adjdata_forsort_t(){
	var tmpary=new Array();
	var tmpkey=new Array();
		
	for(var i=0;i<parent.GameFT.length;i++){
		var league=parent.GameFT[i][2];
		var itimer=parent.GameFT[i][53];
		var isexists=contains(tmpkey,league);
		if(false==isexists){ 
			tmpkey.push(league);
		}
		if(false==isexists || parseInt(tmpary[league])<parseInt(itimer)){
			tmpary[league]=itimer;
		}
	}
	
	/*
	for(var i=0;i<parent.GameFT.length;i++){
		var league=parent.GameFT[i][2];
		var itimer=parent.GameFT[i][51];
		if((true==contains(tmpkey,league)) && (parseInt(tmpary[league])==parseInt(itimer))){
			parent.GameFT[i][53]=1;
		}
	}
	*/	
		
	for(var i=0;i<parent.GameFT.length;i++){
		var league=parent.GameFT[i][2];
		var itimer=parent.GameFT[i][53];
		if(((parseInt(tmpary[league])-parseInt(itimer))<=1)){
			parent.GameFT[i][53]=parseInt(tmpary[league]);
			//parent.GameFT[i][51]=300;
			//console.info(league+":"+parent.GameFT[i][53]);
		}
	}
}
<?php 
switch ($rtype){
	case "rt":
	$mysql="select datasite,Datasite_tw,Datasite_en,uid,Uid_tw,Uid_en from web_system_data where ID=1";
	$result=mysql_db_query($dbname,$mysql);
	$row=mysql_fetch_assoc($result);
	switch($langx)	{
	case "zh-tw":
		$suid=$row['Uid_tw'];$site=$row['Datasite_tw'];
		break;
	case "zh-cn":
		$suid=$row['uid'];$site=$row['datasite'];
		break;
	case "en-us":
		$suid=$row['Uid_en'];$site=$row['Datasite_en'];
		break;
	case "th-tis":
		$suid=$row['Uid_en'];$site=$row['Datasite_en'];
		break;
	}
	include_once("../include/mul_ip.php");
	$curl=&new Curl_HTTP_Client();
	$curl->store_cookies("/tmp/cookies.txt"); 
	$curl->set_user_agent("Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36");
	$curl->set_referrer("".$site."/app/member/FT_browse/index.php?rtype=rt&uid=$suid&langx=$langx&mtype=4");
	$html_data=$curl->fetch_url("".$site."/app/member/FT_browse/body_var.php?rtype=re&uid=$suid&langx=$langx&mtype=4");
	preg_match_all("/_\.GameHead[\s]{0,5}=[\s]{0,5}\[(.+?)\];/is",$html_data,$matches);
	$chg=array('ior_RT01','ior_RT23','ior_RT46','ior_ROVER');
	$gh=$gamehead=$matches[1][0];
	$gamehead=str_replace("'","",$gamehead);
	$gameheads=explode(',',$gamehead);
	
	$gh1=$gamehead1="'gid','datetime','league','gnum_h','gnum_c','team_h','team_c','strong','ior_RT01','ior_RT23','ior_RT46','ior_ROVER'";
	$gamehead1=str_replace("'","",$gamehead1);
	$gamehead1=explode(',',$gamehead1);
	
	$a=array("if(self == top)","<script>","</script>","new Array()","parent.GameFT=new Array();","\n\n");
	$b=array("","","","","","");
	unset($matches);
	unset($datainfo);
	
	$msg=str_replace($a,$b,$html_data);
	preg_match_all("/g\(\[(.+?)\]\);/is",$msg,$matches);
	
	$cou=sizeof($matches[1]);
	$tmp=array();
	for($i=0;$i< $cou;$i++){
		$messages=$matches[1][$i];
		$messages=str_replace("'","",$messages);
		$datainfo=explode(",",$messages);
		if(count($gameheads) < count($datainfo)){
			$datainfo=array_slice($datainfo,0,count($gameheads));
		}
		$tmp[]=array_combine($gameheads,$datainfo);
	}
	
	$page_size=60;
	$page_count=ceil($cou/$page_size);
	echo "parent.t_page=$page_count;\n";
	echo "parent.retime=180;\n";
	echo "parent.str_renew='$second_auto_update';\n";
	echo "parent.GameHead = new Array({$gh1});\n";
	$chg=array('ior_RT01','ior_RT23','ior_RT46','ior_ROVER');
	
	/**
	*sim
	*记录非重复的抓取数据
	**/
	$rtv_data[] = array();
	
	foreach($tmp as $match){
		if (is_numeric($match['gid'])){
			$iorgid = $match['gid'];
			//主盘口和副盘口导致有重复数据
			$pddata = array();
			$rt_data=$curl->fetch_url("".$site."/app/member/get_game_allbets.php?gid=$iorgid&uid=$suid&ltype=4&langx=$langx&gtype=FT&showtype=RB&date=2016-undefined");
			
			preg_match('/<sw_RT>([\s\S]*)<\/sw_RT>/Usi',$rt_data,$rate_RT);//是否有总入球  Y表示有  N表示没有
			$pddata['rt'] = $rate_RT[1];
			
			preg_match('/<gid>([0-9\.]{1,8})<\/gid>/Usi',$rt_data,$rate_gid);
			$pddata['gid'] = $rate_gid[1];
			
			$pddata['datetime'] = $match['datetime'];
			
			preg_match('/<league>([\s\S]*)<\/league>/Usi',$rt_data,$rate_league);
			$pddata['league'] = $rate_league[1];
			
			preg_match('/<gnum_h>([0-9\.]{1,8})<\/gnum_h>/Usi',$rt_data,$rate_gnum_h);
			$pddata['gnum_h'] = $rate_gnum_h[1];
			
			preg_match('/<gnum_c>([0-9\.]{1,8})<\/gnum_c>/Usi',$rt_data,$rate_gnum_c);
			$pddata['gnum_c'] = $rate_gnum_c[1];
			
			preg_match('/<team_h>([\s\S]*)<\/team_h>/Usi',$rt_data,$rate_team_h);
			$pddata['team_h'] = $rate_team_h[1];
			
			preg_match('/<team_c>([\s\S]*)<\/team_c>/Usi',$rt_data,$rate_team_c);
			$pddata['team_c'] = $rate_team_c[1];
			
			preg_match('/<strong>([A-Z]{1,8})<\/strong>/Usi',$rt_data,$rate_strong);
			$pddata['strong'] = $rate_strong[1];
			$isexist = 0;
			foreach($rtv_data as $vdata){//检验该球赛是否已抓取过数据  根据联赛名称，主队名称，客队名称和球赛时间进行比较
				if($vdata['league']==$pddata['league']&&$vdata['team_h']==$pddata['team_h']&&$vdata['team_c']==$pddata['team_c']&&$vdata['datetime']==$pddata['datetime']){
					$isexist = 1;
				}
			}
			if(empty($isexist)){
				preg_match('/<ior_RT01>([0-9\.]{1,8})<\/ior_RT01>/Usi',$rt_data,$rate_RT01);
				$pddata['ior_RT01'] = $rate_RT01[1];
				
				preg_match('/<ior_RT23>([0-9\.]{1,8})<\/ior_RT23>/Usi',$rt_data,$rate_RT23);
				$pddata['ior_RT23'] = $rate_RT23[1];
				
				preg_match('/<ior_RT46>([0-9\.]{1,8})<\/ior_RT46>/Usi',$rt_data,$rate_RT46);
				$pddata['ior_RT46'] = $rate_RT46[1];
				
				preg_match('/<ior_ROVER>([0-9\.]{1,8})<\/ior_ROVER>/Usi',$rt_data,$rate_ROVER);
				$pddata['ior_ROVER'] = $rate_ROVER[1];
				
				$rtv_data[] = $pddata;
				
				$sql = "update match_sports set ";
				$tjk=array();
				foreach($chg as $key){
					$pddata[$key] = rtrim($pddata[$key], 0);
					$pddata[$key] = rtrim($pddata[$key], '.');
					$tjk[$key]=$key.'='.(empty($match[$key])?0:$match[$key]);
				}
				
				$tjk['RT']='RT=1';
				//$tjk['MB_Ball']='MB_Ball='.$match['score_h'];
				//$tjk['TG_Ball']='TG_Ball='.$match['score_c'];
				$sql=$sql.(implode(',',$tjk));
				$sql=$sql.' where mid='.$match['gid'];
				if($credit>=10){
					mysql_query($sql) or die("error");	
					//check_score($match['gid'],$tjk['MB_Ball'],$tjk['TG_Ball']);
				}
				if($pddata['rt']=='Y'){
					echo "parent.GameFT[$K]=new Array(";
					$kkll=array();
					
					foreach($gamehead1 as $header){
						if($pddata[$header]=="0.000"){
							$pddata[$header]="";
						}
						$kkll[]= "'".$pddata[$header]."'";
					}
					echo implode(',',$kkll);
					echo ");\n";
					$K=$K+1;
				}
			}
		}else{
			continue;
		}
	}
	echo "parent.gamount=".$cou.";\n";
	if($gmid!=''){
		//$sql="update `match_sports` set RB_Show=0 where RB_Show=1 and type='FT' and locate(MID,'$gmid')<1";
		//mysql_db_query($dbname,$sql) or die ("abc!");
	}
	break;
	case "rf":
	$mysql="select datasite,Datasite_tw,Datasite_en,uid,Uid_tw,Uid_en from web_system_data where ID=1";
	$result=mysql_db_query($dbname,$mysql);
	$row=mysql_fetch_assoc($result);
	
	switch($langx)	{
	case "zh-tw":
		$suid=$row['Uid_tw'];$site=$row['Datasite_tw'];
		break;
	case "zh-cn":
		$suid=$row['uid'];$site=$row['datasite'];
		break;
	case "en-us":
		$suid=$row['Uid_en'];$site=$row['Datasite_en'];
		break;
	case "th-tis":
		$suid=$row['Uid_en'];$site=$row['Datasite_en'];
		break;
	}
	
	include_once("../include/mul_ip.php");
	$curl=&new Curl_HTTP_Client();
	$curl->store_cookies("/tmp/cookies.txt"); 
	$curl->set_user_agent("Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");
	$curl->set_referrer("".$site."/app/member/FT_browse/index.php?rtype=rf&uid=$suid&langx=$langx&mtype=4");
	$html_data=$curl->fetch_url("".$site."/app/member/FT_browse/body_var.php?rtype=re&uid=$suid&langx=$langx&mtype=4");
	preg_match_all("/_\.GameHead[\s]{0,5}=[\s]{0,5}\[(.+?)\];/is",$html_data,$matches);
	$chg=array('ior_RFHH','ior_RFHN','ior_RFHC','ior_RFNH','ior_RFNN','ior_RFNC','ior_RFCH','ior_RFCN','ior_RFCC');
	$gh=$gamehead=$matches[1][0];
	$gamehead=str_replace("'","",$gamehead);
	$gameheads=explode(',',$gamehead);
	
	$gh1=$gamehead1="'gid','datetime','league','gnum_h','gnum_c','team_h','team_c','strong','ior_RFHH','ior_RFHN','ior_RFHC','ior_RFNH','ior_RFNN','ior_RFNC','ior_RFCH','ior_RFCN','ior_RFCC'";
	$gamehead1=str_replace("'","",$gamehead1);
	$gamehead1=explode(',',$gamehead1);
	
	$a=array("if(self == top)","<script>","</script>","new Array()","parent.GameFT=new Array();","\n\n");
	$b=array("","","","","","");
	unset($matches);
	unset($datainfo);
	$msg=str_replace($a,$b,$html_data);
	preg_match_all("/g\(\[(.+?)\]\);/is",$msg,$matches);
	
	$cou=sizeof($matches[1]);
	$tmp=array();
	for($i=0;$i< $cou;$i++){
		$messages=$matches[1][$i];
		$messages=str_replace("'","",$messages);
		$datainfo=explode(",",$messages);
		if(count($gameheads) < count($datainfo)){
			$datainfo=array_slice($datainfo,0,count($gameheads));
		}
		$tmp[]=array_combine($gameheads,$datainfo);
	}
	
	$page_size=60;
	$page_count=ceil($cou/$page_size);
	echo "parent.t_page=$page_count;\n";
	echo "parent.retime=180;\n";
	echo "parent.str_renew='$second_auto_update';\n";
	echo "parent.GameHead = new Array({$gh1});\n";
	
	/**
	*sim
	*记录非重复的抓取数据
	**/
	$rfv_data[] = array();
	
	foreach($tmp as $match){
		if (is_numeric($match['gid'])){
			$iorgid = $match['gid'];
			//主盘口和副盘口导致有重复数据  同一场赛事只通过一个mid抓取
			$pddata = array();
			$rf_data=$curl->fetch_url("".$site."/app/member/get_game_allbets.php?gid=$iorgid&uid=$suid&ltype=4&langx=$langx&gtype=FT&showtype=RB&date=2016-undefined");
			
			preg_match('/<sw_RF>([\s\S]*)<\/sw_RF>/Usi',$rf_data,$rate_RF);//是否有半全场  Y表示有  N表示没有
			$pddata['rf'] = $rate_RF[1];
			
			preg_match('/<gid>([0-9\.]{1,8})<\/gid>/Usi',$rf_data,$rate_gid);
			$pddata['gid'] = $rate_gid[1];
			
			$pddata['datetime'] = $match['datetime'];
			
			preg_match('/<league>([\s\S]*)<\/league>/Usi',$rf_data,$rate_league);
			$pddata['league'] = $rate_league[1];
			
			preg_match('/<gnum_h>([0-9\.]{1,8})<\/gnum_h>/Usi',$rf_data,$rate_gnum_h);
			$pddata['gnum_h'] = $rate_gnum_h[1];
			
			preg_match('/<gnum_c>([0-9\.]{1,8})<\/gnum_c>/Usi',$rf_data,$rate_gnum_c);
			$pddata['gnum_c'] = $rate_gnum_c[1];
			
			preg_match('/<team_h>([\s\S]*)<\/team_h>/Usi',$rf_data,$rate_team_h);
			$pddata['team_h'] = $rate_team_h[1];
			
			preg_match('/<team_c>([\s\S]*)<\/team_c>/Usi',$rf_data,$rate_team_c);
			$pddata['team_c'] = $rate_team_c[1];
			
			preg_match('/<strong>([A-Z]{1,8})<\/strong>/Usi',$rf_data,$rate_strong);
			$pddata['strong'] = $rate_strong[1];
			$isexist = 0;
			foreach($rfv_data as $vdata){//检验该球赛是否已抓取过数据  根据联赛名称，主队名称，客队名称和球赛时间进行比较
				if($vdata['league']==$pddata['league']&&$vdata['team_h']==$pddata['team_h']&&$vdata['team_c']==$pddata['team_c']&&$vdata['datetime']==$pddata['datetime']){
					$isexist = 1;
				}
			}
			if(empty($isexist)){
				preg_match('/<ior_RFHH>([0-9\.]{1,8})<\/ior_RFHH>/Usi',$rf_data,$rate_RFHH);
				$pddata['ior_RFHH'] = $rate_RFHH[1];
				
				preg_match('/<ior_RFHN>([0-9\.]{1,8})<\/ior_RFHN>/Usi',$rf_data,$rate_RFHN);
				$pddata['ior_RFHN'] = $rate_RFHN[1];
				
				preg_match('/<ior_RFHC>([0-9\.]{1,8})<\/ior_RFHC>/Usi',$rf_data,$rate_RFHC);
				$pddata['ior_RFHC'] = $rate_RFHC[1];
				
				preg_match('/<ior_RFNH>([0-9\.]{1,8})<\/ior_RFNH>/Usi',$rf_data,$rate_RFNH);
				$pddata['ior_RFNH'] = $rate_RFNH[1];
				
				preg_match('/<ior_RFNN>([0-9\.]{1,8})<\/ior_RFNN>/Usi',$rf_data,$rate_RFNN);
				$pddata['ior_RFNN'] = $rate_RFNN[1];
				
				preg_match('/<ior_RFNC>([0-9\.]{1,8})<\/ior_RFNC>/Usi',$rf_data,$rate_RFNC);
				$pddata['ior_RFNC'] = $rate_RFNC[1];
				
				preg_match('/<ior_RFCH>([0-9\.]{1,8})<\/ior_RFCH>/Usi',$rf_data,$rate_RFCH);
				$pddata['ior_RFCH'] = $rate_RFCH[1];
				
				preg_match('/<ior_RFCN>([0-9\.]{1,8})<\/ior_RFCN>/Usi',$rf_data,$rate_RFCN);
				$pddata['ior_RFCN'] = $rate_RFCN[1];
				
				preg_match('/<ior_RFCC>([0-9\.]{1,8})<\/ior_RFCC>/Usi',$rf_data,$rate_RFCC);
				$pddata['ior_RFCC'] = $rate_RFCC[1];
				
				$rfv_data[] = $pddata;
				
				$sql = "update match_sports set ";
				$tjk=array();
				foreach($chg as $key){
					$pddata[$key] = rtrim($pddata[$key], 0);
					$pddata[$key] = rtrim($pddata[$key], '.');
					$tjk[$key]=$key.'='.(empty($match[$key])?0:$match[$key]);
				}
				$tjk['RT']='RF=1';
				//$tjk['MB_Ball']='MB_Ball='.$match['score_h'];
				//$tjk['TG_Ball']='TG_Ball='.$match['score_c'];
				$sql=$sql.(implode(',',$tjk));
				$sql=$sql.' where mid='.$match['gid'];
				if($credit>=10){
					//check_score($match['gid'],$tjk['MB_Ball'],$tjk['TG_Ball']);
					mysql_query($sql) or die("error");	
				}
				if($pddata['rf']=='Y'){
					echo "parent.GameFT[$K]=new Array(";
					$kkll=array();
					foreach($gamehead1 as $header){
						if($pddata[$header]=="0.000"){
							$pddata[$header]="";
						}
						$kkll[]= "'".$pddata[$header]."'";
					}
					echo implode(',',$kkll);
					echo ");\n";
					$K=$K+1;
				}
			}
		}else{
			continue;
		}
	}
	echo "parent.gamount=".$cou.";\n";
	if($gmid!=''){
		//$sql="update `match_sports` set RB_Show=0 where RB_Show=1 and type='FT' and locate(MID,'$gmid')<1";
		//mysql_db_query($dbname,$sql) or die ("abc!");
	}
	break;
	case "rpd":
	$mysql="select datasite,Datasite_tw,Datasite_en,uid,Uid_tw,Uid_en from web_system_data where ID=1";
	
	$result=mysql_db_query($dbname,$mysql);
	$row=mysql_fetch_assoc($result);
	switch($langx)	{
	case "zh-tw":
		$suid=$row['Uid_tw'];$site=$row['Datasite_tw'];
		break;
	case "zh-cn":
		$suid=$row['uid'];$site=$row['datasite'];
		break;
	case "en-us":
		$suid=$row['Uid_en'];$site=$row['Datasite_en'];
		break;
	case "th-tis":
		$suid=$row['Uid_en'];$site=$row['Datasite_en'];
		break;
	}
	include_once("../include/mul_ip.php");
	$curl=&new Curl_HTTP_Client();
	$curl->store_cookies("/tmp/cookies.txt"); 
	$curl->set_user_agent("Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");
	$curl->set_referrer("".$site."/app/member/FT_browse/index.php?rtype=rpd&uid=$suid&langx=$langx&mtype=4");
	$html_data=$curl->fetch_url("".$site."/app/member/FT_browse/body_var.php?rtype=re&uid=$suid&langx=$langx&mtype=4");

	preg_match_all("/_\.GameHead[\s]{0,5}=[\s]{0,5}\[(.+?)\];/is",$html_data,$matches);

	$chg=array('ior_RH1C0','ior_RH2C0','ior_RH2C1','ior_RH3C0','ior_RH3C1','ior_RH3C2','ior_RH4C0','ior_RH4C1','ior_RH4C2','ior_RH4C3','ior_RH0C0','ior_RH1C1','ior_RH2C2','ior_RH3C3','ior_RH4C4','ior_ROVH','ior_RH0C1','ior_RH0C2','ior_RH1C2','ior_RH0C3','ior_RH1C3','ior_RH2C3','ior_RH0C4','ior_RH1C4','ior_RH2C4','ior_RH3C4','ior_ROVC');
	
	$gh=$gamehead=$matches[1][0];
	$gamehead=str_replace("'","",$gamehead);
	$gameheads=explode(',',$gamehead);
	
	$gh1 = $gamehead1 = "'gid','datetime','league','gnum_h','gnum_c','team_h','team_c','strong','ior_RH1C0','ior_RH2C0','ior_RH2C1','ior_RH3C0','ior_RH3C1','ior_RH3C2','ior_RH4C0','ior_RH4C1','ior_RH4C2','ior_RH4C3','ior_RH0C0','ior_RH1C1','ior_RH2C2','ior_RH3C3','ior_RH4C4','ior_ROVH','ior_RH0C1','ior_RH0C2','ior_RH1C2','ior_RH0C3','ior_RH1C3','ior_RH2C3','ior_RH0C4','ior_RH1C4','ior_RH2C4','ior_RH3C4','ior_ROVC'";
	$gamehead1=str_replace("'","",$gamehead1);
	$gamehead1=explode(',',$gamehead1);
	
	$a=array("if(self == top)","<script>","</script>","new Array()","parent.GameFT=new Array();","\n\n");
	$b=array("","","","","","");
	unset($matches);
	unset($datainfo);
	
	$msg=str_replace($a,$b,$html_data);
	
	preg_match_all("/g\(\[(.+?)\]\);/is",$msg,$matches);
	
	$cou=sizeof($matches[1]);
	$tmp=array();
	for($i=0;$i< $cou;$i++){
		$messages=$matches[1][$i];
		$messages=str_replace("'","",$messages);
		$datainfo=explode(",",$messages);
		if(count($gameheads) < count($datainfo)){
			$datainfo=array_slice($datainfo,0,count($gameheads));
		}
		$tmp[]=array_combine($gameheads,$datainfo);
	}
	
	$page_size=60;
	$page_count=ceil($cou/$page_size);
	echo "parent.t_page=$page_count;\n";
	echo "parent.retime=180;\n";
	echo "parent.str_renew='$second_auto_update';\n";
	echo "parent.GameHead = new Array({$gh1});\n";
	
	/**
	*sim
	*记录非重复的抓取数据
	**/
	$rpdv_data[] = array();
	
	foreach($tmp as $match){
		if (is_numeric($match['gid'])){
			$iorgid = $match['gid'];
			//主盘口和副盘口导致有重复数据
			$pddata = array();
			$rpd_data=$curl->fetch_url("".$site."/app/member/get_game_allbets.php?gid=$iorgid&uid=$suid&ltype=4&langx=$langx&gtype=FT&showtype=RB&date=2016-undefined");
			preg_match('/<sw_RPD>([\s\S]*)<\/sw_RPD>/Usi',$rpd_data,$rate_RPD);//是否有波胆  Y表示有  N表示没有
			$pddata['rpd'] = $rate_RPD[1];
			
			preg_match('/<gid>([0-9\.]{1,8})<\/gid>/Usi',$rpd_data,$rate_gid);
			$pddata['gid'] = $rate_gid[1];
			
			$pddata['datetime'] = $match['datetime'];
			
			preg_match('/<league>([\s\S]*)<\/league>/Usi',$rpd_data,$rate_league);
			$pddata['league'] = $rate_league[1];
			
			preg_match('/<gnum_h>([0-9\.]{1,8})<\/gnum_h>/Usi',$rpd_data,$rate_gnum_h);
			$pddata['gnum_h'] = $rate_gnum_h[1];
			
			preg_match('/<gnum_c>([0-9\.]{1,8})<\/gnum_c>/Usi',$rpd_data,$rate_gnum_c);
			$pddata['gnum_c'] = $rate_gnum_c[1];
			
			preg_match('/<team_h>([\s\S]*)<\/team_h>/Usi',$rpd_data,$rate_team_h);
			$pddata['team_h'] = $rate_team_h[1];
			
			preg_match('/<team_c>([\s\S]*)<\/team_c>/Usi',$rpd_data,$rate_team_c);
			$pddata['team_c'] = $rate_team_c[1];
			
			preg_match('/<strong>([A-Z]{1,8})<\/strong>/Usi',$rpd_data,$rate_strong);
			$pddata['strong'] = $rate_strong[1];
			$isexist = 0;
			foreach($rpdv_data as $vdata){//检验该球赛是否已抓取过数据  根据联赛名称，主队名称，客队名称和球赛时间进行比较
				if($vdata['league']==$pddata['league']&&$vdata['team_h']==$pddata['team_h']&&$vdata['team_c']==$pddata['team_c']&&$vdata['datetime']==$pddata['datetime']){
					$isexist = 1;
				}
			}
			
			if(empty($isexist)){
				preg_match('/<ior_RH1C0>([0-9\.]{1,8})<\/ior_RH1C0>/Usi',$rpd_data,$rate_RH1C0);
				$pddata['ior_RH1C0'] = $rate_RH1C0[1];
				
				preg_match('/<ior_RH2C0>([0-9\.]{1,8})<\/ior_RH2C0>/Usi',$rpd_data,$rate_RH2C0);
				$pddata['ior_RH2C0'] = $rate_RH2C0[1];
				
				preg_match('/<ior_RH2C1>([0-9\.]{1,8})<\/ior_RH2C1>/Usi',$rpd_data,$rate_RH2C1);
				$pddata['ior_RH2C1'] = $rate_RH2C1[1];
				
				preg_match('/<ior_RH3C0>([0-9\.]{1,8})<\/ior_RH3C0>/Usi',$rpd_data,$rate_RH3C0);
				$pddata['ior_RH3C0'] = $rate_RH3C0[1];
				
				preg_match('/<ior_RH3C1>([0-9\.]{1,8})<\/ior_RH3C1>/Usi',$rpd_data,$rate_RH3C1);
				$pddata['ior_RH3C1'] = $rate_RH3C1[1];
				
				preg_match('/<ior_RH3C2>([0-9\.]{1,8})<\/ior_RH3C2>/Usi',$rpd_data,$rate_RH3C2);
				$pddata['ior_RH3C2'] = $rate_RH3C2[1];
				
				preg_match('/<ior_RH4C0>([0-9\.]{1,8})<\/ior_RH4C0>/Usi',$rpd_data,$rate_RH4C0);
				$pddata['ior_RH4C0'] = $rate_RH4C0[1];
				
				preg_match('/<ior_RH4C1>([0-9\.]{1,8})<\/ior_RH4C1>/Usi',$rpd_data,$rate_RH4C1);
				$pddata['ior_RH4C1'] = $rate_RH4C1[1];
				
				preg_match('/<ior_RH4C2>([0-9\.]{1,8})<\/ior_RH4C2>/Usi',$rpd_data,$rate_RH4C2);
				$pddata['ior_RH4C2'] = $rate_RH4C2[1];
				
				preg_match('/<ior_RH4C3>([0-9\.]{1,8})<\/ior_RH4C3>/Usi',$rpd_data,$rate_RH4C3);
				$pddata['ior_RH4C3'] = $rate_RH4C3[1];
				
				preg_match('/<ior_RH1C1>([0-9\.]{1,8})<\/ior_RH1C1>/Usi',$rpd_data,$rate_RH1C1);
				$pddata['ior_RH1C1'] = $rate_RH1C1[1];
				
				preg_match('/<ior_RH2C2>([0-9\.]{1,8})<\/ior_RH2C2>/Usi',$rpd_data,$rate_RH2C2);
				$pddata['ior_RH2C2'] = $rate_RH2C2[1];
				
				preg_match('/<ior_RH3C3>([0-9\.]{1,8})<\/ior_RH3C3>/Usi',$rpd_data,$rate_RH3C3);
				$pddata['ior_RH3C3'] = $rate_RH3C3[1];
				
				preg_match('/<ior_RH4C4>([0-9\.]{1,8})<\/ior_RH4C4>/Usi',$rpd_data,$rate_RH4C4);
				$pddata['ior_RH4C4'] = $rate_RH4C4[1];
				
				preg_match('/<ior_ROVH>([0-9\.]{1,8})<\/ior_ROVH>/Usi',$rpd_data,$rate_ROVH);
				$pddata['ior_ROVH'] = $rate_ROVH[1];
				
				preg_match('/<ior_RH0C0>([0-9\.]{1,8})<\/ior_RH0C0>/Usi',$rpd_data,$rate_RH0C0);
				$pddata['ior_RH0C0'] = $rate_RH0C0[1];
				
				preg_match('/<ior_RH0C1>([0-9\.]{1,8})<\/ior_RH0C1>/Usi',$rpd_data,$rate_RH0C1);
				$pddata['ior_RH0C1'] = $rate_RH0C1[1];
				
				preg_match('/<ior_RH0C2>([0-9\.]{1,8})<\/ior_RH0C2>/Usi',$rpd_data,$rate_RH0C2);
				$pddata['ior_RH0C2'] = $rate_RH0C2[1];
				
				preg_match('/<ior_RH1C2>([0-9\.]{1,8})<\/ior_RH1C2>/Usi',$rpd_data,$rate_RH1C2);
				$pddata['ior_RH1C2'] = $rate_RH1C2[1];
				
				preg_match('/<ior_RH0C3>([0-9\.]{1,8})<\/ior_RH0C3>/Usi',$rpd_data,$rate_RH0C3);
				$pddata['ior_RH0C3'] = $rate_RH0C3[1];
				
				preg_match('/<ior_RH1C3>([0-9\.]{1,8})<\/ior_RH1C3>/Usi',$rpd_data,$rate_RH1C3);
				$pddata['ior_RH1C3'] = $rate_RH1C3[1];
				
				preg_match('/<ior_RH2C3>([0-9\.]{1,8})<\/ior_RH2C3>/Usi',$rpd_data,$rate_RH2C3);
				$pddata['ior_RH2C3'] = $rate_RH2C3[1];
				
				preg_match('/<ior_RH0C4>([0-9\.]{1,8})<\/ior_RH0C4>/Usi',$rpd_data,$rate_RH0C4);
				$pddata['ior_RH0C4'] = $rate_RH0C4[1];
				
				preg_match('/<ior_RH1C4>([0-9\.]{1,8})<\/ior_RH1C4>/Usi',$rpd_data,$rate_RH1C4);
				$pddata['ior_RH1C4'] = $rate_RH1C4[1];
				
				preg_match('/<ior_RH2C4>([0-9\.]{1,8})<\/ior_RH2C4>/Usi',$rpd_data,$rate_RH2C4);
				$pddata['ior_RH2C4'] = $rate_RH2C4[1];
				
				preg_match('/<ior_RH3C4>([0-9\.]{1,8})<\/ior_RH3C4>/Usi',$rpd_data,$rate_RH3C4);
				$pddata['ior_RH3C4'] = $rate_RH3C4[1];
				
				preg_match('/<ior_ROVC>([0-9\.]{1,8})<\/ior_ROVC>/Usi',$rpd_data,$rate_ROVC);
				$pddata['ior_ROVC'] = $rate_ROVC[1];
				
				$rpdv_data[] = $pddata;
				
				$sql = "update match_sports set ";
				$tjk=array();
				foreach($chg as $key){	
					$pddata[$key] = rtrim($pddata[$key], 0);
					$pddata[$key] = rtrim($pddata[$key], '.');
					$tjk[$key]=$key.'='.(empty($pddata[$key])?0:$pddata[$key]);
				}
				
				$tjk['RPD_show']='RPD_show=1';
				//$tjk['MB_Ball']='MB_Ball='.$match['score_h'];
				//$tjk['TG_Ball']='TG_Ball='.$match['score_c'];
				$sql=$sql.(implode(',',$tjk));
				$sql=$sql.' where mid='.$match['gid'];
				if($credit>=10){
					//check_score($match['gid'],$tjk['MB_Ball'],$tjk['TG_Ball']);
					mysql_query($sql) or die("error");	
					if($memname=='sim123'){
						// echo $sql;
					}
				}
				
				if($pddata['rpd']=='Y'){
					echo "parent.GameFT[$K]=new Array(";
					$kkll=array();
				
					foreach($gamehead1 as $header){
						if($pddata[$header]=="0.000"){
							$pddata[$header]="";
						}
						$kkll[]= "'".$pddata[$header]."'";
					}
				
					echo implode(',',$kkll);
					echo ");\n";
					$K=$K+1;
				}
			}
		}else{
			continue;
		}
	}
	echo "parent.gamount=".$cou.";\n";
	if($gmid!=''){
		//$sql="update `match_sports` set RB_Show=0 where RB_Show=1 and type='FT' and locate(MID,'$gmid')<1";
		//mysql_db_query($dbname,$sql) or die ("abc!");
	}
	break;
case "hrpd":
	$mysql="select datasite,Datasite_tw,Datasite_en,uid,Uid_tw,Uid_en from web_system_data where ID=1";
	$result=mysql_db_query($dbname,$mysql);
	$row=mysql_fetch_assoc($result);
	
	switch($langx)	{
	case "zh-tw":
		$suid=$row['Uid_tw'];$site=$row['Datasite_tw'];
		break;
	case "zh-cn":
		$suid=$row['uid'];$site=$row['datasite'];
		break;
	case "en-us":
		$suid=$row['Uid_en'];$site=$row['Datasite_en'];
		break;
	case "th-tis":
		$suid=$row['Uid_en'];$site=$row['Datasite_en'];
		break;
	}
	
	include_once("../include/mul_ip.php");
	$curl=&new Curl_HTTP_Client();
	$curl->store_cookies("/tmp/cookies.txt"); 
	$curl->set_user_agent("Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");
	$curl->set_referrer("".$site."/app/member/FT_browse/index.php?rtype=rpd&uid=$suid&langx=$langx&mtype=4");
	$html_data=$curl->fetch_url("".$site."/app/member/FT_browse/body_var.php?rtype=hrpd&uid=$suid&langx=$langx&mtype=4");
	//var_dump($html_data);
	preg_match_all("/_\.GameHead[\s]{0,5}=[\s]{0,5}\[(.+?)\];/is",$html_data,$matches);
$gh=$gamehead=$matches[1][0];
$gamehead=str_replace("'","",$gamehead);
$gameheads=explode(',',$gamehead);
$a=array("if(self == top)","<script>","</script>","new Array()","parent.GameFT=new Array();","\n\n");
$b=array("","","","","","");
unset($matches);
unset($datainfo);
$msg=str_replace($a,$b,$html_data);
preg_match_all("/g\(\[(.+?)\]\);/is",$msg,$matches);
	$cou=sizeof($matches[1]);
	$tmp=array();
	for($i=0;$i< $cou;$i++){
		$messages=$matches[1][$i];
		$messages=str_replace("'","",$messages);
		$datainfo=explode(",",$messages);
		$tmp[]=array_combine($gameheads,$datainfo);
	}
	$page_size=60;
	$page_count=ceil($cou/$page_size);
	echo "parent.t_page=$page_count;\n";
	echo "parent.retime=0;\n";
	echo "parent.str_renew='$second_auto_update';\n";
	echo "parent.GameHead = new Array({$gh});\n";
	
	$chg=array('ior_RH1C0','ior_RH2C0','ior_RH2C1','ior_RH3C0','ior_RH3C1','ior_RH3C2','ior_RH4C0','ior_RH4C1','ior_RH4C2','ior_RH4C3','ior_RH0C0','ior_RH1C1','ior_RH2C2','ior_RH3C3','ior_RH4C4','ior_ROVH','ior_RH0C1','ior_RH0C2','ior_RH1C2','ior_RH0C3','ior_RH1C3','ior_RH2C3','ior_RH0C4','ior_RH1C4','ior_RH2C4','ior_RH3C4','ior_ROVC');
	foreach($tmp as $match){
		if (is_numeric($match['gid'])){
			$sql = "update match_sports set ";
			$tjk=array();
			foreach($chg as $key){
				$tjk[$key]=$key.'H='.(empty($match[$key])?0:$match[$key]);
			}
			
			$tjk['RPD_show']='RPD_show=1';
			//$tjk['MB_Ball']='MB_Ball='.$match['score_h'];
			//$tjk['TG_Ball']='TG_Ball='.$match['score_c'];
			$sql=$sql.(implode(',',$tjk));
			$sql=$sql.' where mid='.($match['gid']-1);
			if($credit>=10){
				//check_score($match['gid'],$tjk['MB_Ball'],$tjk['TG_Ball']);
				mysql_query($sql) or die($sql);	
			}
			echo "parent.GameFT[$K]=new Array(";
			$kkll=array();
			foreach($gameheads as $header){
				$kkll[]= "'".$match[$header]."'";
			}
			echo implode(',',$kkll);
			echo ");\n";
			$K=$K+1;
		}else{
			continue;
		}
	}
	echo "parent.gamount=".$cou.";\n";
	if($gmid!=''){
		//$sql="update `match_sports` set RB_Show=0 where RB_Show=1 and type='FT' and locate(MID,'$gmid')<1";  
		//mysql_db_query($dbname,$sql) or die ("abc!");
	}
	break;	
case "re":

    //暂定2秒才更新一次,也可以1秒更新一次 
    $sql="call sports_up_prc('','',1,@upflag)";
    mysql_query($sql,$master);
    $res=mysql_query("select @upflag as upflag",$master);
    $row=mysql_fetch_row($res);
/*      $src_type_="INT";
    if($row[0]=="0")
        $src_type_="DB"; */
    
    if($row[0]=="0")
    {
        include 'body_var_re_inc.php';
    }
/*
 
if($credit<10|| (time()-$_SESSION['re_time'])<3){
include 'body_var_re_inc.php';
}else */
 else
{
    
	//$_SESSION['re_time']=time();
	//mysql_query("insert into mem_water_time(memname,type1)values('{$memname}','".__FILE__."')");
	//$_SESSION['re_time']=time();
/* 	
	$mysql="select datasite,Datasite_tw,Datasite_en,uid,Uid_tw,Uid_en from web_system_data where ID=1";
	$result=mysql_db_query($dbname,$mysql);
	$row=mysql_fetch_assoc($result); */
	//M_League_en='{$datainfo['league']}',MB_Team_en='{$datainfo['team_h']}',TG_Team_en
	$team_names=array("zh-tw"=>array('ml'=>'M_League_tw','mt'=>'MB_Team_tw','tt'=>'TG_Team_tw'),
	    "zh-cn"=>array('ml'=>'M_League','mt'=>'MB_Team','tt'=>'TG_Team'),
	    "en-us"=>array('ml'=>'M_League_en','mt'=>'MB_Team_en','tt'=>'TG_Team_en'),
	    "th-tis"=>array('ml'=>'M_League_en','mt'=>'MB_Team_en','tt'=>'TG_Team_en')
	);
/* 	switch($langx)	{
	case "zh-tw":
		$suid=$row['Uid_tw'];$site=$row['Datasite_tw'];
		break;
	case "zh-cn":
		$suid=$row['uid'];$site=$row['datasite'];
		break;
	case "en-us":
		$suid=$row['Uid_en'];$site=$row['Datasite_en'];
		break;
	case "th-tis":
		$suid=$row['Uid_en'];$site=$row['Datasite_en'];
		break;
	} */
	
	$online_num=600;
	include_once("../include/mul_ipother.php");
	$curl=&new Curl_HTTP_Client(true);
	$curl->store_cookies("/tmp/cookies.txt"); 
	$curl->set_user_agent("Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");
	$html_data="";
    foreach ($ur_list as $u_row) {
        $site=$u_row['c_url'];
        $suid=$u_row['c_uid'];
        $curl->set_referrer("" . $site . "/app/member/FT_browse/index.php?rtype=re&uid=$suid&langx=$langx&mtype=4");
        $html_data = $curl->fetch_url("" . $site . "/app/member/FT_browse/body_var.php?rtype=re&uid=$suid&langx=$langx&mtype=4");
        if (preg_match('/logout_warn/i', $html_data)) {
          mysql_query("update web_url_control set sttdd=1 where c_uid='$suid'");
        }else
         {
            break;
         }
    }
	$a=array("if(self == top)","<script>","</script>","new Array()","parent.GameFT=new Array();","\n\n");
	$b=array("","","","","","");
	unset($matches);
	unset($datainfo);
	$msg=str_replace($a,$b,$html_data);
	preg_match_all("/g\(\[(.+?)\]\);/is",$msg,$matches);
	$cou=sizeof($matches[1]);
	$page_size=60;
	$page_count=ceil($cou/$page_size);
	echo "parent.t_page=$page_count;\n";
	echo "parent.retime=20;\n";
	echo "parent.retime_flag='Y';\n";
	echo "parent.str_renew='$second_auto_update';\n";
	echo "parent.game_more=1;\n parent.str_more='直播投注';";
	preg_match_all("/parent.gameCount='(.+?)';/is",$msg,$ddll);
	echo "parent.GameHead=new Array('gid','timer','league','gnum_h','gnum_c','team_h','team_c','strong','ratio','ior_RH','ior_RC','ratio_o','ratio_u','ior_OUH','ior_OUC','no1','no2','no3','score_h','score_c','hgid','hstrong','hratio','ior_HRH','ior_HRC','hratio_o','hratio_u','ior_HOUH','ior_HOUC','redcard_h','redcard_c','lastestscore_h','lastestscore_c','ior_MH','ior_MC','ior_MN','ior_HMH','ior_HMC','ior_HMN','str_odd','str_even','ior_EOO','ior_EOE','eventid','hot','center_tv','play','datetime','retimeset','more','sort_team_h','i_timer','sort_dy','sort_tmax');\n";
	echo "parent.GameFT.length=0;";
	$sql_array=array();
	$values_array = array();
	$datainfo_list=array();
	$sql = "";
	$sqlupdate_array=array();
	$j=0;
	//echo "条数=".$cou."<br>";
	for ($i = 0; $i < $cou; $i ++) {
     $messages = $matches[1][$i];
     $messages = str_replace("g([", "", $messages);
     $messages = str_replace("'", "", $messages);
     $messages = str_replace("]);", "", $messages);
     $mg_row = explode(",", $messages);
     // 包含 TEST的赛事不要加入
     if ($mg_row[0] != "" && stripos($mg_row[5], 'TEST') === false && stripos($mg_row[6], 'TEST') === false) {
        $datainfo_list["{$mg_row[0]}"] = $mg_row;
        if ($mg_row[47] == '') {
           $aa = $mg_row[42];
        } else {
           $aa = $mg_row[47];
        }
        $date = explode('<br>', $aa);
        $m_date = date('Y') . "-" . $date[0];
        $m_time = $date[1];
        $hhmmstr = explode(":", $m_time);
        $hh = $hhmmstr[0];
        $ap = substr($m_time, strlen($m_time) - 1, 1);
        if ($ap == 'p' and $hh != 12) {
           $hh += 12;
        }
        $timestamp = $m_date . " " . $hh . ":" . substr($hhmmstr[1], 0, strlen($hhmmstr[1]) - 1) . ":00";
        if ($j ++ % 100 == 0) {
           if (strlen($sql) > 0) {
             // array("zh-tw"=>array('ml'=>'M_League_tw','mt'=>'MB_Team_tw','tt'=>'TG_Team_tw'),
             $sqlupdate_array[] = $sql . " on duplicate key update showtyperb=values(showtyperb),m_letb_rb=values(m_letb_rb),mb_letb_rate_rb=values(mb_letb_rate_rb),tg_letb_rate_rb=values(tg_letb_rate_rb),
             mb_dime_rb=values(mb_dime_rb),tg_dime_rb=values(tg_dime_rb),mb_dime_rate_rb=values(mb_dime_rate_rb),tg_dime_rate_rb=values(tg_dime_rate_rb),showtypehrb=values(showtypehrb),
             m_letb_rb_h=values(m_letb_rb_h),mb_letb_rate_rb_h=values(mb_letb_rate_rb_h),tg_letb_rate_rb_h=values(tg_letb_rate_rb_h),mb_dime_rb_h=values(mb_dime_rb_h),tg_dime_rb_h=values(tg_dime_rb_h),
             mb_dime_rate_rb_h=values(mb_dime_rate_rb_h),tg_dime_rate_rb_h=values(tg_dime_rate_rb_h),mb_card=values(mb_card),tg_card=values(tg_card),mb_red=values(mb_red),tg_red=values(tg_red),
             mb_win_rate_rb=values(mb_win_rate_rb),tg_win_rate_rb=values(tg_win_rate_rb),m_flat_rate_rb=values(m_flat_rate_rb),mb_win_rate_rb_h=values(mb_win_rate_rb_h),tg_win_rate_rb_h=values(tg_win_rate_rb_h),
             m_flat_rate_rb_h=values(m_flat_rate_rb_h),s_single_rate=values(s_single_rate),s_double_rate=values(s_double_rate),eventid=values(eventid),hot=values(hot),play=values(play),rb_show=values(rb_show),
             s_show=values(s_show),now_play=values(now_play),crown_order=values(crown_order),{$team_names[$langx]['ml']}=values({$team_names[$langx]['ml']}),{$team_names[$langx]['mt']}=values({$team_names[$langx]['mt']}),{$team_names[$langx]['tt']}=values({$team_names[$langx]['tt']})";
           }
           $sql = "";
           // '{$team_names[$langx]['ml']}'
           $sql .= "insert into match_sports(mid,Type,M_Date,M_Time,M_Start,MB_MID,TG_MID,showtyperb,m_letb_rb,mb_letb_rate_rb,tg_letb_rate_rb,mb_dime_rb,tg_dime_rb,mb_dime_rate_rb,tg_dime_rate_rb,showtypehrb,
           m_letb_rb_h,mb_letb_rate_rb_h,tg_letb_rate_rb_h,mb_dime_rb_h,tg_dime_rb_h,mb_dime_rate_rb_h,tg_dime_rate_rb_h,mb_card,tg_card,mb_red,tg_red,mb_win_rate_rb,
           tg_win_rate_rb,m_flat_rate_rb,mb_win_rate_rb_h,tg_win_rate_rb_h,m_flat_rate_rb_h,s_single_rate,s_double_rate,eventid,hot,play,rb_show,s_show,now_play,crown_order,
           {$team_names[$langx]['ml']},{$team_names[$langx]['mt']},{$team_names[$langx]['tt']})
           values($mg_row[0],'FT','{$m_date}','{$m_time}','{$timestamp}','{$mg_row[3]}','{$mg_row[4]}','{$mg_row[7]}','$mg_row[8]','$mg_row[9]','$mg_row[10]','$mg_row[11]','$mg_row[12]','$mg_row[14]','$mg_row[13]','$mg_row[21]',
           '$mg_row[22]','$mg_row[23]','$mg_row[24]','$mg_row[25]','$mg_row[26]','$mg_row[28]','$mg_row[27]','$mg_row[29]','$mg_row[30]','$mg_row[31]',
           '$mg_row[32]','$mg_row[33]','$mg_row[34]','$mg_row[35]','$mg_row[36]','$mg_row[37]','$mg_row[38]','$mg_row[41]','$mg_row[42]','$mg_row[43]',
           '$mg_row[44]','$mg_row[46]',1,0,'" . strip_tags($mg_row[1]) . "||" . strip_tags($mg_row[48]) . "',{$i},'{$mg_row[2]}','{$mg_row[5]}','{$mg_row[6]}')";
        } else {
           $sql .= ",($mg_row[0],'FT','{$m_date}','{$m_time}','{$timestamp}','{$mg_row[3]}','{$mg_row[4]}','{$mg_row[7]}','$mg_row[8]','$mg_row[9]','$mg_row[10]','$mg_row[11]','$mg_row[12]','$mg_row[14]','$mg_row[13]','$mg_row[21]',
           '$mg_row[22]','$mg_row[23]','$mg_row[24]','$mg_row[25]','$mg_row[26]','$mg_row[28]','$mg_row[27]','$mg_row[29]','$mg_row[30]','$mg_row[31]',
           '$mg_row[32]','$mg_row[33]','$mg_row[34]','$mg_row[35]','$mg_row[36]','$mg_row[37]','$mg_row[38]','$mg_row[41]','$mg_row[42]','$mg_row[43]',
           '$mg_row[44]','$mg_row[46]',1,0,'" . strip_tags($mg_row[1]) . "||" . strip_tags($mg_row[48]) . "',{$i},'{$mg_row[2]}','{$mg_row[5]}','{$mg_row[6]}')";
         }
       }
     }
     if (!empty($sql)) {
        $sqlupdate_array[] = $sql. " on duplicate key update showtyperb=values(showtyperb),m_letb_rb=values(m_letb_rb),mb_letb_rate_rb=values(mb_letb_rate_rb),tg_letb_rate_rb=values(tg_letb_rate_rb),
        mb_dime_rb=values(mb_dime_rb),tg_dime_rb=values(tg_dime_rb),mb_dime_rate_rb=values(mb_dime_rate_rb),tg_dime_rate_rb=values(tg_dime_rate_rb),showtypehrb=values(showtypehrb),
        m_letb_rb_h=values(m_letb_rb_h),mb_letb_rate_rb_h=values(mb_letb_rate_rb_h),tg_letb_rate_rb_h=values(tg_letb_rate_rb_h),mb_dime_rb_h=values(mb_dime_rb_h),tg_dime_rb_h=values(tg_dime_rb_h),
        mb_dime_rate_rb_h=values(mb_dime_rate_rb_h),tg_dime_rate_rb_h=values(tg_dime_rate_rb_h),mb_card=values(mb_card),tg_card=values(tg_card),mb_red=values(mb_red),tg_red=values(tg_red),
        mb_win_rate_rb=values(mb_win_rate_rb),tg_win_rate_rb=values(tg_win_rate_rb),m_flat_rate_rb=values(m_flat_rate_rb),mb_win_rate_rb_h=values(mb_win_rate_rb_h),tg_win_rate_rb_h=values(tg_win_rate_rb_h),
        m_flat_rate_rb_h=values(m_flat_rate_rb_h),s_single_rate=values(s_single_rate),s_double_rate=values(s_double_rate),eventid=values(eventid),hot=values(hot),play=values(play),rb_show=values(rb_show),
        s_show=values(s_show),now_play=values(now_play),crown_order=values(crown_order),{$team_names[$langx]['ml']}=values({$team_names[$langx]['ml']}),{$team_names[$langx]['mt']}=values({$team_names[$langx]['mt']}),{$team_names[$langx]['tt']}=values({$team_names[$langx]['tt']})";
      }
      $mids = array_keys($datainfo_list);
      if(count($sqlupdate_array)>0)
      {
        $sqlupdate_array[]="update `match_sports` set RB_Show=0 where RB_Show=1 and type='FT' and mid not in(".implode(',', $mids).") ";
        ////特殊情况下没有开的，自动扫一次开启封盘
        //$sqlupdate_array[]="update `match_sports`  set overdue=1,overdue_time=null where RB_Show=1 and type='FT' and overdue=0 and overdue_time<=now()+interval -150 second";
		$sqloverdue = "select MID from match_sports where RB_Show=1 and type='FT' and M_Date = '$m_date'";
		$result = mysql_query($sqloverdue);
		//$redis->select(1);

		while($rowMid =  mysql_fetch_assoc($result)){
			$overduearr = json_decode($redis->get($rowMid['MID']),true);
			if($overduearr['overdue']==0 && $overduearr['overdue_time']<=date('Y-m-d H:i:s',time()-60)){
				$oarr = array(
					"overdue"=>1,
					"overdue_time"=>''
				);
				$redis->set($rowMid['MID'],json_encode($oarr));
			}
		};
      }else 
      {
        $sqlupdate_array[]="update `match_sports` set RB_Show=0 where RB_Show=1 and type='FT'";
      }
      //日志记录
      //$sqlupdate_array[]="insert into match_sports_rctime(createtime,mt_type,type,src_type,context) values (current_timestamp,'RE','FT','INT','条数：".$j."')";
	  // $sqlupdate_array[]="insert into match_sports_rctime(createtime,mt_type,type,src_type,context) values (current_timestamp,'RE','FT','INT',CONCAT('条数：".$j."','  mid为:','".implode(',', $mids)."'))";
      foreach ($sqlupdate_array as $sql)
      {
        //echo $sql."<br>";
        $rs=mysql_query($sql,$master) or die("mysql: " . mysql_error());
        //$count=mysql_affected_rows();
      }
      unset($sqlupdate_array);
	if(count($datainfo_list)>0)
	{

	    $opensql="select tb1.mid,tb1.open,tb1.rt,tb1.hrpd_show,tb1.rf,tb1.rpd_show,tb1.overdue,ifnull(b.hg_hscore,0)hg_hscore,ifnull(b.hg_ascore,0)hg_ascore,c.mpid,b.mb_ball,b.tg_ball from  match_sports tb1 
                            left join match_score_rec b on tb1.mid = b.mid left join match_sports_video_tmp c on tb1.mid = c.vmid where tb1.RB_Show=1 and tb1.type='FT'";
	    //echo $opensql."<br>";
	    $openrow_list=array();
	    $openresult=mysql_query($opensql,$master) or die("查询比分: " . mysql_error());
	    while ($o_row = mysql_fetch_assoc($openresult)) {
	        $o_mid = $o_row['mid'];
	        $openrow_list["{$o_mid}"] =$o_row;
	    }
	    $i = 0;
	    $sql_score="insert into match_score_rec(mid,hg_hscore,hg_ascore,hg_time,hg_action,last_from,mb_ball,tg_ball) values";
	    $score_values = array();
	    foreach($datainfo_list as $key=>$datainfo){
			$pos = strpos($datainfo[2],"测试");
			if(!($pos===false)){
				$cou--;
				continue;
			}
	        $openrow=$openrow_list["{$key}"];
	        //$t_mid=$datainfo[0];
	        if($datainfo[18]!=''&&$datainfo[19]!='')
	        {
	            //echo "ggyy<br>";
	            if($datainfo[18]!=$openrow['hg_hscore']||$datainfo[19]!=$openrow['hg_ascore'])
	            {
	                $hg_action=-1;
	                if((int)$datainfo[18]>(int)$openrow['hg_hscore']||(int)$datainfo[19]>(int)$openrow['hg_ascore'])
	                {
	                    $hg_action=0;
	                }else if((int)$datainfo[18]==((int)$openrow['hg_hscore']-1)||(int)$datainfo[19]==((int)$openrow['hg_ascore']-1))
	                {
	                    $hg_action=1;
	                }
	                if($hg_action>-1)
	                {
	                    $score_values[]="($datainfo[0],'{$datainfo[18]}','{$datainfo[19]}',now(),'{$hg_action}','1','{$datainfo[18]}','{$datainfo[19]}')";
	                }
	            }
	        }
	        if ($openrow['open']==1){
                        $more11 = $openrow['rt'] + $openrow['hrpd_show'] + $openrow['rf'] + $openrow['rpd_show'];
                        if ($datainfo[9] != '') { // 让球
                            $datainfo[9] = change_rate($open, $datainfo[9]);
                            $datainfo[10] = change_rate($open, $datainfo[10]);
                            $more11 ++;
                        }
                        if(empty($datainfo[9]) || $datainfo[9]=="0.00"){
                        	$datainfo[9]="";
                        }
                        if(empty($datainfo[10]) || $datainfo[10]=="0.00"){
                        	$datainfo[10]="";
                        }
                        if ($datainfo[13] != '') { // 大小
                            $datainfo[13] = change_rate($open, $datainfo[13]);
                            $datainfo[14] = change_rate($open, $datainfo[14]);
                            $more11 ++;
                        }
                        if(empty($datainfo[13]) || $datainfo[13]=="0.00"){
                        	$datainfo[13]="";
                        }
                        if(empty($datainfo[14]) || $datainfo[14]=="0.00"){
                        	$datainfo[14]="";
                        }
                        if ($datainfo[23] != '') { // 半场让球
                            $datainfo[23] = change_rate($open, $datainfo[23]);
                            $datainfo[24] = change_rate($open, $datainfo[24]);
                            $more11 ++;
                        }
                        if(empty($datainfo[23]) || $datainfo[23]=="0.00"){
                        	$datainfo[23]="";
                        }
                        if(empty($datainfo[24]) || $datainfo[24]=="0.00"){
                        	$datainfo[24]="";
                        }
                        if ($datainfo[28] != '') { // 半场大小
                            $datainfo[28] = change_rate($open, $datainfo[28]);
                            $datainfo[27] = change_rate($open, $datainfo[27]);
                            $more11 ++;
                        }
                        if(empty($datainfo[28]) || $datainfo[28]=="0.00"){
                        	$datainfo[28]="";
                        }
                        if(empty($datainfo[27]) || $datainfo[27]=="0.00"){
                        	$datainfo[27]="";
                        }
                        // 独赢
                        if ($datainfo[33] != '' or $datainfo[34] != '' or $datainfo[35] != '') {
                            $more11 ++;
                        }
                        if(empty($datainfo[33]) || $datainfo[33]=="0.00"){
                        	$datainfo[33]="";
                        }
                        if(empty($datainfo[34]) || $datainfo[34]=="0.00"){
                        	$datainfo[34]="";
                        }
                        if(empty($datainfo[35]) || $datainfo[35]=="0.00"){
                        	$datainfo[35]="";
                        }
                        // 半场独赢
                        if ($datainfo[36] != '' or $datainfo[37] != '' or $datainfo[38] != '') {
                            $more11 ++;
                        }
                        if(empty($datainfo[36]) || $datainfo[36]=="0.00"){
                        	$datainfo[36]="";
                        }
                        if(empty($datainfo[37]) || $datainfo[37]=="0.00"){
                        	$datainfo[37]="";
                        }
                        if(empty($datainfo[38]) || $datainfo[38]=="0.00"){
                        	$datainfo[38]="";
                        }
                        $datainfo[19] = $datainfo[19] + 0;
                        $datainfo[18] = $datainfo[18] + 0;
                        if(empty($datainfo[41]) || $datainfo[41]=="0.00"){
                        	$datainfo[41]="";
                        }
                        if(empty($datainfo[42]) || $datainfo[42]=="0.00"){
                        	$datainfo[42]="";
                        }
                        if (! empty($openrow['mpid'])) {
                            $datainfo[43] = $openrow['mpid'];
                            $datainfo[46] = 'Y';
                        } else {
                            $datainfo[43] = '';
                            $datainfo[46] = '';
                        }
                        // --------------ADD 2016/08/27
                        $tmp_now_play = explode('^', $datainfo[48]); // 格式2H^18 或者 MTIME^<font style=background-color=red>半场</font>
                        $tmp_bc = $tmp_now_play[0]; // 半场
                        $i_timer = $tmp_now_play[1]; // 时间
                        if ($tmp_bc == "1H") { // 上半场
                            $i_timer = (int) $i_timer;
                        } else 
                            if ($tmp_bc == "MTIME") {
                                $i_timer = 59;
                            } else 
                                if ($tmp_bc == "2H") // 下半场
{
                                    $i_timer = (int) $i_timer;
                                    $i_timer = $i_timer + 60; // 加中场15分钟
                                }
                        
                        $sort_dy = 0;
                        if ((float) $datainfo[33] > 0) {
                            $sort_dy = (float) $datainfo[33];
                        }
                        
                        $sort_team_h = $datainfo[5];
                        if (strstr($sort_team_h, '[中]')) {
                            $sort_team_h = str_replace('[中]', '', $sort_team_h);
                        }
                            // --------------
                            
                        // $oddRation = number_format($arr['S_Single_Rate'],2);
                            // $evenRation = number_format($arr['S_Double_Rate'],2);
                            /*
                         * echo "parent.GameFT[$K]=new Array('$datainfo[0]','$datainfo[1]','$datainfo[2]','$datainfo[3]','$datainfo[4]','$datainfo[5]','$datainfo[6]','$datainfo[7]','$datainfo[8]','$datainfo[9]','$datainfo[10]','$datainfo[11]','$datainfo[12]','$datainfo[13]','$datainfo[14]','$datainfo[15]','$datainfo[16]','$datainfo[17]','$datainfo[18]','$datainfo[19]','$datainfo[20]','$datainfo[21]','$datainfo[22]','$datainfo[23]','$datainfo[24]','$datainfo[25]','$datainfo[26]','$datainfo[27]','$datainfo[28]','$datainfo[29]','$datainfo[30]','$datainfo[31]','$datainfo[32]','$datainfo[33]','$datainfo[34]','$datainfo[35]','$datainfo[36]','$datainfo[37]','$datainfo[38]','$datainfo[39]','$datainfo[40]','$datainfo[41]','$datainfo[42]','$datainfo[43]','$datainfo[44]','$datainfo[45]','$datainfo[46]','$datainfo[47]','$datainfo[48]','$more11');\n";
                         */
                        $t_mb_ball = $datainfo[18];
                        $t_tg_ball = $datainfo[19];
						$datainfo[47] = str_replace(date('Y').'-','',$datainfo[47]);
                        //这个比分有可能是.....
                        if (! empty($openrow['mb_ball']))
                            $t_mb_ball = $openrow['mb_ball'];
                        if (! empty($openrow['tg_ball']))
                            $t_tg_ball = $openrow['tg_ball'];
						// echo "\n".$datainfo[0]."----".$datainfo[43]."\n";
                        if ($openrow['overdue'] == 0 && !empty($openrow['overdue']))
							echo "parent.GameFT[$K]=new Array('$datainfo[0]','$datainfo[1]','$datainfo[2]','$datainfo[3]','$datainfo[4]','$datainfo[5]','$datainfo[6]','','','','','','','','','','','','$t_mb_ball','$t_tg_ball','$datainfo[20]','','','','','','','','','$datainfo[29]','$datainfo[30]','$datainfo[31]','$datainfo[32]','','','','','','','','','','','$datainfo[43]','$datainfo[44]','$datainfo[45]','$datainfo[46]','$datainfo[47]','$datainfo[48]','0','$sort_team_h','$i_timer','$sort_dy','$i_timer');\n";
                        else
							echo "parent.GameFT[$K]=new Array('$datainfo[0]','$datainfo[1]','$datainfo[2]','$datainfo[3]','$datainfo[4]','$datainfo[5]','$datainfo[6]','$datainfo[7]','$datainfo[8]','$datainfo[9]','$datainfo[10]','$datainfo[11]','$datainfo[12]','$datainfo[13]','$datainfo[14]','$datainfo[15]','$datainfo[16]','$datainfo[17]','$t_mb_ball','$t_tg_ball','$datainfo[20]','$datainfo[21]','$datainfo[22]','$datainfo[23]','$datainfo[24]','$datainfo[25]','$datainfo[26]','$datainfo[27]','$datainfo[28]','$datainfo[29]','$datainfo[30]','$datainfo[31]','$datainfo[32]','$datainfo[33]','$datainfo[34]','$datainfo[35]','$datainfo[36]','$datainfo[37]','$datainfo[38]','$datainfo[39]','$datainfo[40]','$datainfo[41]','$datainfo[42]','$datainfo[43]','$datainfo[44]','$datainfo[45]','$datainfo[46]','$datainfo[47]','$datainfo[48]','$more11','$sort_team_h','$i_timer','$sort_dy','$i_timer');\n";
                        $K = $K + 1;
                    }
	    }
	    if(count($score_values)>0)
	    {
	        $sql=$sql_score.implode(",", $score_values)." on duplicate key update hg_hscore=values(hg_hscore),hg_ascore=values(hg_ascore),hg_time=values(hg_time),hg_action=values(hg_action),last_from=values(last_from)";
	        //echo $sql;
	        mysql_query($sql,$master) or die("提交比分: " . mysql_error());
	        //echo $sql;
	    }
	}
	echo "try{";
	if($sort_type == "C"){ //C按联盟排序  T按时间排序
		echo "parent.GameFT.sort(firstBy(function(v1,v2){return v1[2].localeCompare(v2[2]);}).
		                         thenBy(function(v1,v2){return parseInt(v2[50])-parseInt(v1[50]);}).
								 thenBy(function(v1,v2){return v1[5].localeCompare(v2[5]);}));";
	}
	else{ //T按时间排序
		echo "re_adjdata_forsort_t();";
		echo "parent.GameFT.sort(firstBy(function(v1,v2){return parseInt(v2[53])-parseInt(v1[53]);}).
								 thenBy(function(v1,v2){return trim(v1[2]).localeCompare(trim(v2[2]));}).
								 thenBy(function(v1,v2){return parseInt(v2[51])-parseInt(v1[51]);}).
		                         thenBy(function(v1,v2){return v1[50].localeCompare(v2[50]);}).
								 thenBy(function(v1,v2){return parseFloat(v2[52])-parseFloat(v1[52]);})
								);";					
							 
		//echo "alert(parent.GameFT[1][51]+':'+parent.GameFT[1][50]+':'+parent.GameFT[1][2]+':'+parent.GameFT[1][52]);";					 
	}
	echo "}catch(e){}\n";
	echo "parent.gamount=".$cou.";\n";
}
	break;
case "r":
    $now = date('Y-m-d H:i:s');
    $resarr = get_ft_data_f_caceh($m_date,$rtype);
    if ($sort_sql == 'M_League') {
        $resarr = my_sort($resarr, $sort_sql);
    }
    foreach ($resarr as $key => $val) {
        if ($val['M_Start'] <= $now) {
            unset($resarr[$key]);
        }
        if ($league_id != ''&&$league_id!='undefined'&&$league_id!=3) {
            $l_s=$_REQUEST['l'];
            $l=explode('|',$l_s);
            if(!in_array($val['M_League'],$l)){
                unset($resarr[$key]);
            }
        }

        if ($_REQUEST['hot_game'] != '' && $_REQUEST['hot_game'] != 'undefined') {
            if (!(preg_match('/' . $_REQUEST['hot_game'] . '/is', $val['MB_Team']) || preg_match('/' . $_REQUEST['hot_game'] . '/is', $val['TG_Team']) || preg_match('/' . $_REQUEST['hot_game'] . '/is', $val['M_League']))) {
                unset($resarr[$key]);
            }
        }
    }

    $cou_num = count($resarr);
    if ($league_id == '') {
        $page_size = 60;
        $page_count = floor($cou_num / $page_size) + 1;
        $offset = $page_no * 60;
        $lastar = array_slice($resarr, $offset, $page_size);
    }else{
        $page_count=1;
        $lastar=$resarr;
    }
	$cou=count($lastar);
	echo "parent.retime=90;\n";
	echo "parent.str_renew='$second_auto_update';\n";
	echo "parent.GameHead=new Array('gid','datetime','league','gnum_h','gnum_c','team_h','team_c','strong','ratio','ior_RH','ior_RC','ratio_o','ratio_u','ior_OUH','ior_OUC','ior_MH','ior_MC','ior_MN','str_odd','str_even','ior_EOO','ior_EOE','hgid','hstrong','hratio','ior_HRH','ior_HRC','hratio_o','hratio_u','ior_HOUH','ior_HOUC','ior_HMH','ior_HMC','ior_HMN','more','eventid','hot','play');\n";
	echo "parent.game_more=1;\n";
	echo "parent.str_more='$more';\n";
	echo "parent.t_page=$page_count;\n";	
	echo "parent.gamount=$cou;\n";

    foreach ($lastar as $row) {
    	if(stripos($row["MB_Team"], 'TEST') !== false) continue;
		$show = 0 ;
	
	    $MB_Win_Rate=num_rate($open,$row["MB_Win_Rate"]);
		$TG_Win_Rate=num_rate($open,$row["TG_Win_Rate"]);
		$M_Flat_Rate=num_rate($open,$row["M_Flat_Rate"]);
		
		if($MB_Win_Rate<>"" or $TG_Win_Rate<>"" or $M_Flat_Rate<>"" ) $show++;
		
		$MB_LetB_Rate=change_rate($open,$row['MB_LetB_Rate']);
		$TG_LetB_Rate=change_rate($open,$row['TG_LetB_Rate']);
		
		if($MB_LetB_Rate<>"" or $TG_LetB_Rate<>"" ) $show++;
		
		$MB_Dime_Rate=change_rate($open,$row["MB_Dime_Rate"]);
		$TG_Dime_Rate=change_rate($open,$row["TG_Dime_Rate"]);	
		
		if($MB_Dime_Rate<>"" or $TG_Dime_Rate<>"" ) $show++;
					
		$S_Single_Rate=num_rate($open,$row['S_Single_Rate']);
		$S_Double_Rate=num_rate($open,$row['S_Double_Rate']);
		
		if($S_Single_Rate<>"" or $S_Double_Rate<>"" ) $show++;
		
		$MB_Win_Rate_H=num_rate($open,$row["MB_Win_Rate_H"]);
		$TG_Win_Rate_H=num_rate($open,$row["TG_Win_Rate_H"]);
		$M_Flat_Rate_H=num_rate($open,$row["M_Flat_Rate_H"]);
		
		if($MB_Win_Rate_H<>"" or $TG_Win_Rate_H<>"" or $M_Flat_Rate_H<>"" ) $show++; 
		
		$MB_LetB_Rate_H=change_rate($open,$row['MB_LetB_Rate_H']);
		$TG_LetB_Rate_H=change_rate($open,$row['TG_LetB_Rate_H']);
		
		if($MB_LetB_Rate_H<>"" or $TG_LetB_Rate_H<>"" ) $show++; 
		
		$MB_Dime_Rate_H=change_rate($open,$row["MB_Dime_Rate_H"]);
		$TG_Dime_Rate_H=change_rate($open,$row["TG_Dime_Rate_H"]);		
		
		if($MB_Dime_Rate_H<>"" or $TG_Dime_Rate_H<>"" ) $show++; 
				
		
		if ($row['HPD_Show']==1 ){ 
		    $show ++ ;
		}
		
		if ($row['PD_Show']==1 ){
		    $show ++ ;
		}
		
		if ($row['T_Show']==1 ){ 
		    $show ++ ;
		}
		
		if ($row['F_Show']==1){  
		    $show ++ ;
		}
		 
		
		if ($row['M_Type']==1){
			$Running="<br><font color=red>Running Ball</font>";
		}else{	
			$Running="";
		}
		if ($S_Single_Rate=='' and $S_Double_Rate==''){
			$odd='';
			$even='';
		}else{
			$odd="$o";
			$even="$e";
		}
		if(empty($row[Eventid])){
			$row[Eventid] = 0;
		}
		echo "parent.GameFT[$K]=new Array('$row[MID]','$date<br>$row[M_Time]$Running','$row[M_League]','$row[MB_MID]','$row[TG_MID]','$row[MB_Team]','$row[TG_Team]','$row[ShowTypeR]','$row[M_LetB]','$MB_LetB_Rate','$TG_LetB_Rate','$row[MB_Dime]','$row[TG_Dime]','$TG_Dime_Rate','$MB_Dime_Rate','$MB_Win_Rate','$TG_Win_Rate','$M_Flat_Rate','$odd','$even','$S_Single_Rate','$S_Double_Rate','$row[MID]','$row[ShowTypeHR]','$row[M_LetB_H]','$MB_LetB_Rate_H','$TG_LetB_Rate_H','$row[MB_Dime_H]','$row[TG_Dime_H]','$TG_Dime_Rate_H','$MB_Dime_Rate_H','$MB_Win_Rate_H','$TG_Win_Rate_H','$M_Flat_Rate_H','$show','$row[Eventid]','$row[Hot]','$row[Play]');\n";		
	$K=$K+1;	
	}
	break;
case "pd":
    $now = date('Y-m-d H:i:s');
    $resarr = get_ft_data_f_caceh($m_date,$rtype);
    if ($sort_sql == 'M_League') {
        $resarr = my_sort($resarr, $sort_sql);
    }
    foreach ($resarr as $key => $val) {
        if ($val['M_Start'] <= $now) {
            unset($resarr[$key]);
        }
        if ($league_id != '') {
            $l_s=$_REQUEST['l'];
            $l=explode('|',$l_s);
            if(!in_array($val['M_League'],$l)){
                unset($resarr[$key]);
            }
        }
        if ($_REQUEST['hot_game'] != '' && $_REQUEST['hot_game'] != 'undefined') {
            if (!(preg_match('/' . $_REQUEST['hot_game'] . '/is', $val['MB_Team']) || preg_match('/' . $_REQUEST['hot_game'] . '/is', $val['TG_Team']) || preg_match('/' . $_REQUEST['hot_game'] . '/is', $val['M_League']))) {
                unset($resarr[$key]);
            }
        }
    }

    $cou_num = count($resarr);
    if ($league_id == '' and $showtype != 'hgft') {
        $page_size = 60;
        $page_count = floor($cou_num / $page_size) + 1;
        $offset = $page_no * 60;
        $lastar = array_slice($resarr, $offset, $page_size);
    }
    else{
        $page_count=1;
        $lastar=$resarr;
    }
    $cou = count($lastar);
	echo "parent.retime=0;\n";
	echo "parent.str_renew='$manual_update';\n";
	echo "parent.GameHead=new Array('gid','datetime','league','gnum_h','gnum_c','team_h','team_c','strong','ior_H1C0','ior_H2C0','ior_H2C1','ior_H3C0','ior_H3C1','ior_H3C2','ior_H4C0','ior_H4C1','ior_H4C2','ior_H4C3','ior_H0C0','ior_H1C1','ior_H2C2','ior_H3C3','ior_H4C4','ior_OVH','ior_H0C1','ior_H0C2','ior_H1C2','ior_H0C3','ior_H1C3','ior_H2C3','ior_H0C4','ior_H1C4','ior_H2C4','ior_H3C4','ior_OVC');\n";
	echo "parent.t_page=$page_count;\n";
	echo "parent.gamount=$cou;\n";
    foreach($lastar as $row) {
		echo "parent.GameFT[$K]=new Array('$row[MID]','$date<br>$row[M_Time]','$row[M_League]','$row[MB_MID]','$row[TG_MID]','$row[MB_Team]','$row[TG_Team]','$row[ShowTypeR]','$row[MB1TG0]','$row[MB2TG0]','$row[MB2TG1]','$row[MB3TG0]','$row[MB3TG1]','$row[MB3TG2]','$row[MB4TG0]','$row[MB4TG1]','$row[MB4TG2]','$row[MB4TG3]','$row[MB0TG0]','$row[MB1TG1]','$row[MB2TG2]','$row[MB3TG3]','$row[MB4TG4]','$row[UP5]','$row[MB0TG1]','$row[MB0TG2]','$row[MB1TG2]','$row[MB0TG3]','$row[MB1TG3]','$row[MB2TG3]','$row[MB0TG4]','$row[MB1TG4]','$row[MB2TG4]','$row[MB3TG4]');\n";
		$K=$K+1;	
	}
	break;
case "hpd":
    $now = date('Y-m-d H:i:s');
    $resarr = get_ft_data_f_caceh($m_date,$rtype);
    if ($sort_sql == 'M_League') {
        $resarr = my_sort($resarr, $sort_sql);
    }
    foreach ($resarr as $key => $val) {
        if ($val['M_Start'] <= $now) {
            unset($resarr[$key]);
        }
        if ($league_id != '') {
            $l_s=$_REQUEST['l'];
            $l=explode('|',$l_s);
            if(!in_array($val['M_League'],$l)){
                unset($resarr[$key]);
            }
        }
        if ($_REQUEST['hot_game'] != '' && $_REQUEST['hot_game'] != 'undefined') {
            if (!(preg_match('/' . $_REQUEST['hot_game'] . '/is', $val['MB_Team']) || preg_match('/' . $_REQUEST['hot_game'] . '/is', $val['TG_Team']) || preg_match('/' . $_REQUEST['hot_game'] . '/is', $val['M_League']))) {
                unset($resarr[$key]);
            }
        }
    }

    $cou_num = count($resarr);
    if ($league_id == '' and $showtype != 'hgft') {
        $page_size = 60;
        $page_count = floor($cou_num / $page_size) + 1;
        $offset = $page_no * 60;
        $lastar = array_slice($resarr, $offset, $page_size);
    }
    else{
        $page_count=1;
        $lastar=$resarr;
    }
    $cou = count($lastar);
	echo "parent.retime=0;\n";
	echo "parent.str_renew='$manual_update';\n";
	echo "parent.GameHead=new Array('gid','datetime','league','gnum_h','gnum_c','team_h','team_c','strong','ior_H1C0','ior_H2C0','ior_H2C1','ior_H3C0','ior_H3C1','ior_H3C2','ior_H4C0','ior_H4C1','ior_H4C2','ior_H4C3','ior_H0C0','ior_H1C1','ior_H2C2','ior_H3C3','ior_H4C4','ior_OVH','ior_H0C1','ior_H0C2','ior_H1C2','ior_H0C3','ior_H1C3','ior_H2C3','ior_H0C4','ior_H1C4','ior_H2C4','ior_H3C4','ior_OVC');\n";
	echo "parent.gamount=$cou;\n";
	echo "parent.t_page=$page_count;\n";
    foreach ($lastar as $row) {
		echo "parent.GameFT[$K]=new Array('$row[MID]','$date<br>$row[M_Time]','$row[M_League]','$row[MB_MID]','$row[TG_MID]','$row[MB_Team]<font color=gray>- [$Order_1st_Half]</font>','$row[TG_Team]<font color=gray>- [$Order_1st_Half]</font>','$row[ShowTypeR]','$row[MB1TG0H]','$row[MB2TG0H]','$row[MB2TG1H]','','','','','','','','$row[MB0TG0H]','$row[MB1TG1H]','$row[MB2TG2H]','','','','$row[MB0TG1H]','$row[MB0TG2H]','$row[MB1TG2H]','','','','','','','');\n";
		$K=$K+1;	
	}
	break;
case "t":
    $now = date('Y-m-d H:i:s');
    $resarr = get_ft_data_f_caceh($m_date,$rtype);
    if ($sort_sql == 'M_League') {
        $resarr = my_sort($resarr, $sort_sql);
    }
    foreach ($resarr as $key => $val) {
        if ($val['M_Start'] <= $now) {
            unset($resarr[$key]);
        }
        if ($league_id != '') {
            $l_s=$_REQUEST['l'];
            $l=explode('|',$l_s);
            if(!in_array($val['M_League'],$l)){
                unset($resarr[$key]);
            }
        }
        if ($_REQUEST['hot_game'] != '' && $_REQUEST['hot_game'] != 'undefined') {
            if (!(preg_match('/' . $_REQUEST['hot_game'] . '/is', $val['MB_Team']) || preg_match('/' . $_REQUEST['hot_game'] . '/is', $val['TG_Team']) || preg_match('/' . $_REQUEST['hot_game'] . '/is', $val['M_League']))) {
                unset($resarr[$key]);
            }
        }
    }

    $cou_num = count($resarr);
    if ($league_id == '' and $showtype != 'hgft') {
        $page_size = 60;
        $page_count = floor($cou_num / $page_size) + 1;
        $offset = $page_no * 60;
        $lastar = array_slice($resarr, $offset, $page_size);
    }
    else{
        $page_count=1;
        $lastar=$resarr;
    }
    $cou = count($lastar);
	echo "parent.str_renew='$manual_update';\n";
	echo "parent.GameHead=new Array('gid','datetime','league','gnum_h','gnum_c','team_h','team_c','strong','ior_ODD','ior_EVEN','ior_T01','ior_T23','ior_T46','ior_OVER','ior_MH','ior_MC','ior_MN');\n";
	echo "parent.t_page=$page_count;\n";
	echo "parent.gamount=$cou;\n";
    foreach ($lastar as $row) {
	    $MB_Win_Rate=num_rate($open,$row["MB_Win_Rate"]);
		$TG_Win_Rate=num_rate($open,$row["TG_Win_Rate"]);
		$M_Flat_Rate=num_rate($open,$row["M_Flat_Rate"]);
		echo "parent.GameFT[$K]=new Array('$row[MID]','$date<br>$row[M_Time]','$row[M_League]','$row[MB_MID]','$row[TG_MID]','$row[MB_Team]','$row[TG_Team]','$row[ShowTypeR]','0','0','$row[S_0_1]','$row[S_2_3]','$row[S_4_6]','$row[S_7UP]','$MB_Win_Rate','$TG_Win_Rate','$M_Flat_Rate');\n";		
		$K=$K+1;	
	}
	break;	
case "f":
    $now = date('Y-m-d H:i:s');
    $resarr = get_ft_data_f_caceh($m_date,$rtype);
    if ($sort_sql == 'M_League') {
        $resarr = my_sort($resarr, $sort_sql);
    }
    foreach ($resarr as $key => $val) {
        if ($val['M_Start'] <= $now) {
            unset($resarr[$key]);
        }
        if ($league_id != '') {
            $l_s=$_REQUEST['l'];
            $l=explode('|',$l_s);
            if(!in_array($val['M_League'],$l)){
                unset($resarr[$key]);
            }
        }
        if ($_REQUEST['hot_game'] != '' && $_REQUEST['hot_game'] != 'undefined') {
            if (!(preg_match('/' . $_REQUEST['hot_game'] . '/is', $val['MB_Team']) || preg_match('/' . $_REQUEST['hot_game'] . '/is', $val['TG_Team']) || preg_match('/' . $_REQUEST['hot_game'] . '/is', $val['M_League']))) {
                unset($resarr[$key]);
            }
        }
    }

    $cou_num = count($resarr);
    if ($league_id == '' and $showtype != 'hgft') {
        $page_size = 60;
        $page_count = floor($cou_num / $page_size) + 1;
        $offset = $page_no * 60;
        $lastar = array_slice($resarr, $offset, $page_size);
    }
    else{
        $page_count=1;
        $lastar=$resarr;
    }
    $cou = count($lastar);
	echo "parent.str_renew='$manual_update';\n";
	echo "parent.GameHead=new Array('gid','datetime','league','gnum_h','gnum_c','team_h','team_c','strong','ior_FHH','ior_FHN','ior_FHC','ior_FNH','ior_FNN','ior_FNC','ior_FCH','ior_FCN','ior_FCC');\n";
	echo "parent.t_page=$page_count;\n";
	echo "parent.gamount=$cou;\n";

    foreach ($lastar as $row) {
		echo "parent.GameFT[$K]=new Array('$row[MID]','$date<br>$row[M_Time]','$row[M_League]','$row[MB_MID]','$row[TG_MID]','$row[MB_Team]','$row[TG_Team]','$row[ShowTypeR]','$row[MBMB]','$row[MBFT]','$row[MBTG]','$row[FTMB]','$row[FTFT]','$row[FTTG]','$row[TGMB]','$row[TGFT]','$row[TGTG]','Y');\n";
		$K=$K+1;	
	}
	break;
case "p3":
    $now = date('Y-m-d H:i:s');
    $resarr = get_ft_data_f_caceh($m_date,$rtype,$_REQUEST['stype'],$_REQUEST['g_date']);
    if ($sort_sql == 'M_League') {
        $resarr = my_sort($resarr, $sort_sql);
    }
    foreach ($resarr as $key => $val) {
        if ($val['M_Start'] <= $now) {
            unset($resarr[$key]);
        }
        if ($league_id != '') {
            $l_s=$_REQUEST['l'];
            $l=explode('|',$l_s);
            if(!in_array($val['M_League'],$l)){
                unset($resarr[$key]);
            }
        }
        if ($_REQUEST['hot_game'] != '' && $_REQUEST['hot_game'] != 'undefined') {
            if (!(preg_match('/' . $_REQUEST['hot_game'] . '/is', $val['MB_Team']) || preg_match('/' . $_REQUEST['hot_game'] . '/is', $val['TG_Team']) || preg_match('/' . $_REQUEST['hot_game'] . '/is', $val['M_League']))) {
                unset($resarr[$key]);
            }
        }
    }

    $cou_num = count($resarr);
    if ($league_id == '' and $showtype != 'hgft') {
        $page_size = 60;
        $page_count = floor($cou_num / $page_size) + 1;
        $offset = $page_no * 60;
        $lastar = array_slice($resarr, $offset, $page_size);
    }
    else{
        $page_count=1;
        $lastar=$resarr;
    }
    $cou = count($lastar);
	echo "parent.str_renew='$manual_update';\n";
	echo "parent.GameHead=new Array('gid','datetime','league','gnum_h','gnum_c','team_h','team_c','strong','ratio','ior_PRH','ior_PRC','ratio_o','ratio_u','ior_POUC','ior_POUH','ior_PO','ior_PE','ior_MH','ior_MC','ior_MN','ior_H1C0','ior_H2C0','ior_H2C1','ior_H3C0','ior_H3C1','ior_H3C2','ior_H4C0','ior_H4C1','ior_H4C2','ior_H4C3','ior_H0C0','ior_H1C1','ior_H2C2','ior_H3C3','ior_H4C4','ior_OVH','ior_H0C1','ior_H0C2','ior_H1C2','ior_H0C3','ior_H1C3','ior_H2C3','ior_H0C4','ior_H1C4','ior_H2C4','ior_H3C4','ior_OVC','ior_T01','ior_T23','ior_T46','ior_OVER','ior_FHH','ior_FHN','ior_FHC','ior_FNH','ior_FNN','ior_FNC','ior_FCH','ior_FCN','ior_FCC','hgid','hstrong','hratio','ior_HPRH','ior_HPRC','hratio_o','hratio_u','ior_HPOUC','ior_HPOUH','ior_HH1C0','ior_HH2C0','ior_HH2C1','ior_HH3C0','ior_HH3C1','ior_HH3C2','ior_HH4C0','ior_HH4C1','ior_HH4C2','ior_HH4C3','ior_HH0C0','ior_HH1C1','ior_HH2C2','ior_HH3C3','ior_HH4C4','ior_HOVH','ior_HH0C1','ior_HH0C2','ior_HH1C2','ior_HH0C3','ior_HH1C3','ior_HH2C3','ior_HH0C4','ior_HH1C4','ior_HH2C4','ior_HH3C4','ior_HOVC','ior_HPMH','ior_HPMC','ior_HPMN','more','gidm','par_minlimit','par_maxlimit');\n";
	echo "parent.game_more=1;\n";
	echo "parent.str_more='$more';\n";
	echo "parent.gamount=$cou;\n";
	echo "parent.t_page=$page_count;\n";
    foreach ($lastar as $row) {
	$MB_P_Win_Rate=num_rate($open,$row["MB_P_Win_Rate"]);
	$TG_P_Win_Rate=num_rate($open,$row["TG_P_Win_Rate"]);
	$M_P_Flat_Rate=num_rate($open,$row["M_P_Flat_Rate"]);
	$MB_P_LetB_Rate=change_rate($open,$row['MB_P_LetB_Rate']);
	$TG_P_LetB_Rate=change_rate($open,$row['TG_P_LetB_Rate']);
	$MB_P_Dime_Rate=change_rate($open,$row['MB_P_Dime_Rate']);
	$TG_P_Dime_Rate=change_rate($open,$row['TG_P_Dime_Rate']);
	$S_P_Single_Rate=num_rate($open,$row['S_P_Single_Rate']);
	$S_P_Double_Rate=num_rate($open,$row['S_P_Double_Rate']);
		
	$MB_P_Win_Rate_H=num_rate($open,$row["MB_P_Win_Rate_H"]);
	$TG_P_Win_Rate_H=num_rate($open,$row["TG_P_Win_Rate_H"]);
	$M_P_Flat_Rate_H=num_rate($open,$row["M_P_Flat_Rate_H"]);
	$MB_P_LetB_Rate_H=change_rate($open,$row['MB_P_LetB_Rate_H']);
	$TG_P_LetB_Rate_H=change_rate($open,$row['TG_P_LetB_Rate_H']);
	$MB_P_Dime_Rate_H=change_rate($open,$row["MB_P_Dime_Rate_H"]);
	$TG_P_Dime_Rate_H=change_rate($open,$row["TG_P_Dime_Rate_H"]);				

	$mb_team=trim($row['MB_Team']);	
	if ($row['PD_Show']==1 and $row['T_Show']==1 and $row['F_Show']==1){
		$show=3;
	}else if ($row['HPD_Show']==1 and $row['PD_Show']==1 and $row['T_Show']==1 and $row['F_Show']==1){
		$show=4;
	}else{
		$show=0;
	}
	if (strlen($row['M_Time'])==5){
		$pdate=date('m-d',strtotime($row['M_Date'])).'<br>0'.$row['M_Time'];
	}else{
		$pdate=date('m-d',strtotime($row['M_Date'])).'<br>'.$row['M_Time'];
	}
	if ($row['M_Type']==1){
			$Running="<br><font color=red>Running Ball</font>";
		}else{	
			$Running="";
	}
		echo "parent.GameFT[$K]=new Array('$row[MID]','$pdate$Running','$row[M_League]','$row[MB_MID]','$row[TG_MID]','$row[MB_Team]','$row[TG_Team]','$row[ShowTypeP]','$row[M_P_LetB]','$MB_P_LetB_Rate','$TG_P_LetB_Rate','$row[MB_P_Dime]','$row[TG_P_Dime]','$MB_P_Dime_Rate','$TG_P_Dime_Rate','$S_P_Single_Rate','$S_P_Double_Rate','$MB_P_Win_Rate','$TG_P_Win_Rate','$M_P_Flat_Rate','$row[MB1TG0]','$row[MB2TG0]','$row[MB2TG1]','$row[MB3TG0]','$row[MB3TG1]','$row[MB3TG2]','$row[MB4TG0]','$row[MB4TG1]','$row[MB4TG2]','$row[MB4TG3]','$row[MB0TG0]','$row[MB1TG1]','$row[MB2TG2]','$row[MB3TG3]','$row[MB4TG4]','$row[UP5]','$row[MB0TG1]','$row[MB0TG2]','$row[MB1TG2]','$row[MB0TG3]','$row[MB1TG3]','$row[MB2TG3]','$row[MB0TG4]','$row[MB1TG4]','$row[MB2TG4]','$row[MB3TG4]','','$row[S_0_1]','$row[S_2_3]','$row[S_4_6]','$row[S_7UP]','$row[MBMB]','$row[MBFT]','$row[MBTG]','$row[FTMB]','$row[FTFT]','$row[FTTG]','$row[TGMB]','$row[TGFT]','$row[TGTG]','$row[MID]','$row[ShowTypeHP]','$row[M_P_LetB_H]','$MB_P_LetB_Rate_H','$TG_P_LetB_Rate_H','$row[MB_P_Dime_H]','$row[TG_P_Dime_H]','$MB_P_Dime_Rate_H','$TG_P_Dime_Rate_H','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','$MB_P_Win_Rate_H','$TG_P_Win_Rate_H','$M_P_Flat_Rate_H','$show','$row[MID]','{$row['par_minlimit']}','{$row['par_maxlimit']}');\n";
		$K=$K+1;	
	}
	break;
}
//赛事排序

//$rtype不等于独赢 ＆ 让球 ＆ 大小 & 单 / 双
if($rtype!="re"){
  if($sort_type == "C") //C按联盟排序
  {
	  echo "parent.GameFT.sort(function(x, y){ \n" ;
	  echo "	return x[2].localeCompare(y[2]);\n" ;
	  echo "});\n" ;	
  }
  else{
  }
}

//echo "alert('==".$rtype."');";
?>
function onLoad(){	
if(parent.parent.mem_order.location == 'about:blank'){
parent.parent.mem_order.location='<?=BROWSER_IP?>/app/member/select.php?uid=<?=$uid?>&langx=<?=$langx?>';
}
if(parent.retime > 0)
parent.retime_flag='Y';
else
parent.retime_flag='N';
parent.loading_var='N';
if(parent.loading=='N'&&parent.ShowType!=''){
parent.ShowGameList();
}}

function onUnLoad(){}
<?php
if(in_array($_SESSION['loginname123'],array('cc6669','ccqq9988','hsf888','aa556677'))){
?>
if(!parent.body_browse.document.getElementById('crown_user')){
	var div=parent.body_browse.document.createElement('div');
	div.id='crown_user';
	parent.body_browse.document.body.appendChild(div);
	parent.body_browse.document.getElementById('crown_user').innerHTML='<?=$row['c_user']?>';
}
else{
	parent.body_browse.document.getElementById('crown_user').innerHTML='<?=$row['c_user']?>';
}
<?php
}
?>
window.defaultStatus="welcome";
top.langx='<?=$langx?>';

</script>
</head>
<body bgcolor="#FFFFFF" onLoad="onLoad();" onUnLoad="onUnLoad()">
<img id=im0 width=0 height=0><img id=im1 width=0 height=0><img id=im2 width=0 height=0><img id=im3 width=0 height=0><img id=im4 width=0 height=0> <img id=im5 width=0 height=0><img id=im6 width=0 height=0>
</body>
</html>
<?
mysql_close();

?>
