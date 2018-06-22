<?
include "./include/address.mem.php";
require ("./include/config.inc.php");
require ("./include/redis.conf.php");
include "./include/login_session.php";
require ("./include/look_area.php");

$uid=$_REQUEST['uid'];
$langx=$_REQUEST['langx'];
$mtype=$_REQUEST['mtype'];
$showtype=$_REQUEST['showtype'];
require ("include/traditional.$langx.inc.php");

//支付配置获取
include_once('./include/payment.php');

switch ($showtype){
case "future":
	$style='FU';
	$browse="future";
	$button='early';
	break;
case "hgfu":
	$style='FU';
	$browse="future";
	$button='early';
	break;
case "hgft":
	$style='FT';
	$browse="browse";
	$button='today';
	break;
case "fs":
	$style='FS';
	$browse="browse";
	$button='today';
	break;
case "":
	$style='FT';
	$browse="browse";
	$button='today';
	break;
}
switch ($langx){
case 'zh-cn':
    $lan='_cn';
break;
case 'zh-tw':
    $lan='';
break;
case 'en-us':
    $lan='_en';
break;
}
$dlshow=true;
$dl=array('hg8797.com','www.hg8797.com');
if(in_array($_SERVER['HTTP_HOST'],$dl)){
	$dlshow=true;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title></title>
<link rel="stylesheet" href="../../../style/member/mem_header_ft_cn.css?v=20161119" type="text/css">
<SCRIPT language="JavaScript" src="/js/header.js?v=20161229"></SCRIPT>

</head>
<body id="H<?=$style?>" class="bodyset" onLoad="SetRB('FT','<?=$uid?>');onloaded();" >
<div id="container">
  <input type="hidden" id="uid" name="uid" value="<?=$uid?>">
  <input type="hidden" id="langx" name="langx" value="<?=$langx?>">
  <div id="header"><span><h1>&nbsp;</h1></span></div>
  <div id="welcome">
	<ul> 
  	  <!--会员帐号-->
      <li class="name">您好, <strong id="userid"><?=$row['UserName']?></strong>
      	<div id="head_date" style="display:none;"><span id=head_year></span>年<span id=head_month></span>月<span id=head_day></span>日 <span id=head_hour></span>:<span id=head_min></span></div>
      </li>
      <li class="rb" id="rb_btn" style="visibility:hidden;"><span id="rbType"></span><a href="javascript:void(0);" onClick="chg_head('<?=BROWSER_IP?>/app/member/FT_browse/index.php?rtype=re&uid=<?=$uid?>&langx=<?=$langx?>&mtype=4&showtype=',parent.FT_lid_type,'SI2'); chg_button_bg('FT','rb');document.getElementById('msg_btn').className='early';"  id="rbyshow" style="display:;">滚球<span class="rb_sum"> (<span class="game_sum" id="RB_games_FT"></span>)</span></a>
      </li>
      <li class="today" id="today_btn"><span id="todayType" style="display:none;">今日赛事</span><a href="javascript:document.getElementById('msg_btn').className='early';chg_button_bg('FT','today');chg_index('<?=BROWSER_IP?>/app/member/FT_header.php?uid=<?=$uid?>&showtype=&langx=<?=$langx?>&mtype=3','<?=BROWSER_IP?>/app/member/FT_browse/index.php?rtype=r&uid=<?=$uid?>&langx=<?=$langx?>&mtype=3&showtype=future',parent.FT_lid_type,'SI2');" id="todayshow" style="display:;">今日赛事</a></li>
      <li class="early" id="early_btn"><span id="earlyType" style="display:none;">早盘</span> <a href="javascript:document.getElementById('msg_btn').className='early';chg_button_bg('FT','early');chg_index('<?=BROWSER_IP?>/app/member/FT_header.php?uid=<?=$uid?>&showtype=future&langx=<?=$langx?>&mtype=3','<?=BROWSER_IP?>/app/member/FT_future/index.php?rtype=r&uid=<?=$uid?>&langx=<?=$langx?>&mtype=3&showtype=',parent.FU_lid_type,'SI2','future');" id="earlyshow" style="display:'';cursor:hand;" >早盘</a></li>
      <!--<li class="early" id="video_btn"><span id="videoType" style="display:none;">真人视讯</span> <a href="javascript:void(0)" onclick="javascript:document.getElementById('rb_btn').className='rb';document.getElementById('today_btn').className='today';document.getElementById('early_btn').className='early';document.getElementById('video_btn').className='early_on'; window.parent.body.location.href='<?=BROWSER_IP?>/live/live.php?uid=<?=$uid?>&langx=<?=$langx?>';" id="videoshow" style="display:'';cursor:hand;" >真人视讯</a></li>
        <li class="early" ><a href="javascript:void(0)" onclick="window.parent.body.location.href='/gdcp/live.php?uid=<?=$uid?>&langx=<?=$langx?>';"  style="display:'';cursor:hand;" >彩票游戏</a></li>
        <li class="early" ><a href="javascript:void(0)" onclick="window.open('/pt/pt_index.php?uid=<?=$uid?>&langx=<?=$langx?>','','');" style="display:'';cursor:hand;">电子游艺</a></li>-->
                <li class="early" id="msg_btn"><span id="mesType" style="display:none;">消息中心</span><a href="javascript:void(0);" onclick="javascript:document.getElementById('rb_btn').className='today';document.getElementById('today_btn').className='today';document.getElementById('early_btn').className='early';document.getElementById('save_btn').className='early';document.getElementById('msg_btn').className='early_on';window.parent.body.location.href='<?=BROWSER_IP?>/app/member/Message/inbox.php?uid=<?=$uid?>&langx=<?=$langx?>&act=1';">消息中心(<span id="messagenum">0</span>)</a></li>

		<?
if ($row['Pay_Type']==1){
//过滤地区IP
$ip = get_ip();
$ip_arr=explode(",",$ip);
foreach($ip_arr as $ips){
	$ip_area = convertip($ips);
	$ip_area = iconv("GB2312","UTF-8//IGNORE",$ip_area);
	if(strstr($ip_area,'北1京')){
		echo '&nbsp;';
	}else{
?>
        <li class="early" id="save_btn"><a href="javascript:void(0);" onClick="javascript:document.getElementById('rb_btn').className='today';document.getElementById('today_btn').className='today';document.getElementById('early_btn').className='early';document.getElementById('save_btn').className='early_on';document.getElementById('msg_btn').className='early';chg_type('<?=BROWSER_IP?>/app/member/YeePay/register_731.php?uid=<?=$uid?>&langx=<?=$langx?>')">存取款中心</a></li>
        <!--<li class="early_on" id="today_btn"><span id="todayType" style="display:none;">在线存款</span><a href="javascript:void(0);" onClick="chg_button_bg('FT','today');chg_type('<?=$address?>/register.php?uid=<?=$uid?>&langx=<?=$langx?>')">在线存款</a></li>
      <!--<li class="early_on" id="today_btn"><span id="earlyType" style="display:none;">在线提款</span><a href="javascript:void(0);" onClick="chg_button_bg('FT','today');chg_type('<?=BROWSER_IP?>/app/member/YeePay/withdrawal.php?uid=<?=$uid?>&langx=<?=$langx?>')">在线提款</a></li>-->
      <!--<li class="early_on" id="today_btn"><span id="todayType" style="display:none;">存取款记录</span><a href="javascript:void(0);" onClick="chg_button_bg('FT','today');chg_type('<?=BROWSER_IP?>/app/member/YeePay/record.php?uid=<?=$uid?>&langx=<?=$langx?>')">存取款记录</a></li>-->

<?
	}
}
}
?>
      <!--Live TV-->
      <!--<li class="live_tv"><a href="#" onClick="OpenLive()" >&nbsp;</a></li>-->
    </ul>
    <!--<div class="pass"><a href="#" id="chg_pwd" onClick="Go_Chg_pass();" style="cursor:hand">更改密码</a></div>-->
  </div>
  <div id="nav">
    <ul class="level1">

      <li class="ft"><span class="ball"><a href="javascript:chg_button_bg('FT','<?=$button?>');chg_index('<?=BROWSER_IP?>/app/member/FT_header.php?uid=<?=$uid?>&showtype=<?=$showtype?>&langx=<?=$langx?>&mtype=3','<?=BROWSER_IP?>/app/member/FT_<?=$browse?>/index.php?rtype=r&uid=<?=$uid?>&langx=<?=$langx?>&mtype=3&showtype=',parent.FT_lid_type,'SI2');">足球 (<strong class="game_sum" id="FT_games"></strong>)</a></span></li>
      <li class="bk"><span class="ball"><a href="javascript:chg_button_bg('BK','<?=$button?>');chg_index('<?=BROWSER_IP?>/app/member/BK_header.php?uid=<?=$uid?>&showtype=<?=$showtype?>&langx=<?=$langx?>&mtype=3','<?=BROWSER_IP?>/app/member/BK_<?=$browse?>/index.php?rtype=all&uid=<?=$uid?>&langx=<?=$langx?>&mtype=3',parent.BK_lid_type,'SI2');">篮球<span class="ball_nf"></span>美式足球(<strong class="game_sum" id="BK_games"></strong>)</a></span></li>
      <li class="tn"><span class="ball"><a href="javascript:chg_button_bg('TN','<?=$button?>');chg_index('<?=BROWSER_IP?>/app/member/TN_header.php?uid=<?=$uid?>&showtype=<?=$showtype?>&langx=<?=$langx?>&mtype=3','<?=BROWSER_IP?>/app/member/TN_<?=$browse?>/index.php?rtype=r&uid=<?=$uid?>&langx=<?=$langx?>&mtype=3',parent.TN_lid_type,'SI2');">网球 (<strong class="game_sum" id="TN_games"></strong>)</a></span></li>
            <li class="vb"><span class="ball"><a href="javascript:chg_button_bg('VB','<?=$button?>');chg_index('<?=BROWSER_IP?>/app/member/VB_header.php?uid=<?=$uid?>&showtype=<?=$showtype?>&langx=<?=$langx?>&mtype=3','<?=BROWSER_IP?>/app/member/VB_<?=$browse?>/index.php?rtype=r&uid=<?=$uid?>&langx=<?=$langx?>&mtype=3',parent.VB_lid_type,'SI2');">排球 (<strong class="game_sum" id="VB_games"></strong>)</a></span></li>
            <li class="ym"><span class="ball"><a href="javascript:chg_button_bg('BM','<?=$button?>');chg_index('<?=BROWSER_IP?>/app/member/BM_header.php?uid=<?=$uid?>&showtype=<?=$showtype?>&langx=<?=$langx?>&mtype=3','<?=BROWSER_IP?>/app/member/BM_<?=$browse?>/index.php?rtype=r&uid=<?=$uid?>&langx=<?=$langx?>&mtype=3',parent.BM_lid_type,'SI2');"><span class="ball_bad"></span>羽毛球 (<strong class="game_sum" id="BM_games">0</strong>)</a></span></li>
            <li class="pp"><span class="ball"><a href="javascript:chg_button_bg('TT','<?=$button?>');chg_index('<?=BROWSER_IP?>/app/member/TT_header.php?uid=<?=$uid?>&showtype=<?=$showtype?>&langx=<?=$langx?>&mtype=3','<?=BROWSER_IP?>/app/member/TT_<?=$browse?>/index.php?rtype=r&uid=<?=$uid?>&langx=<?=$langx?>&mtype=3',parent.TT_lid_type,'SI2');"><span class="ball_tt"></span>乒乓球 (<strong class="game_sum" id="TT_games">0</strong>)</a></span></li>
      <li class="bs"><span class="ball"><a href="javascript:chg_button_bg('BS','<?=$button?>');chg_index('<?=BROWSER_IP?>/app/member/BS_header.php?uid=<?=$uid?>&showtype=<?=$showtype?>&langx=<?=$langx?>&mtype=3','<?=BROWSER_IP?>/app/member/BS_<?=$browse?>/index.php?rtype=r&uid=<?=$uid?>&langx=<?=$langx?>&mtype=3',parent.BS_lid_type,'SI2');">棒球 (<strong class="game_sum" id="BS_games"></strong>)</a></span></li>
	  <li class="sk"><span><a style="padding:4px 10px !important" href="javascript:chg_button_bg('SK','<?=$button?>');chg_index('<?=BROWSER_IP?>/app/member/SK_header.php?uid=<?=$uid?>&showtype=<?=$showtype?>&langx=<?=$langx?>&mtype=3','<?=BROWSER_IP?>/app/member/SK_<?=$browse?>/index.php?rtype=r&uid=<?=$uid?>&langx=<?=$langx?>&mtype=3',parent.SK_lid_type,'SI2');">斯诺克/台球 (<strong class="game_sum" id="SK_games">0</strong>)</a></span></li>
      <li class="op"><span class="ball"><a href="javascript:chg_button_bg('OP','<?=$button?>');chg_index('<?=BROWSER_IP?>/app/member/OP_header.php?uid=<?=$uid?>&showtype=<?=$showtype?>&langx=<?=$langx?>&mtype=3','<?=BROWSER_IP?>/app/member/OP_<?=$browse?>/index.php?rtype=r&uid=<?=$uid?>&langx=<?=$langx?>&mtype=3',parent.OP_lid_type,'SI2');">其他 (<strong class="game_sum" id="OP_games"></strong>)</a></span></li>
    </ul>
      </li>
    </ul>
  </div>
  <div id="type">
    <ul>
<?
if ($style=='FT'){
?>
      <!--<li class="rb"><a id="rb_class" class="type_out" href="<?=BROWSER_IP?>/app/member/FT_browse/index.php?rtype=re&uid=<?=$uid?>&langx=<?=$langx?>&mtype=3&showtype=" onClick="chg_button_bg('FT','rb');chg_type_class('rb_class');" target="body">滚球<span class="rb_sum"> (<span class="game_sum" id="subRB_games"></span>)</span></a></li>-->
<?
}
?>
      <li class="re"><a id="re_class" class="type_out" href="javascript:void(0);" onClick="chg_button_bg('FT','<?=$button?>');chg_type('<?=BROWSER_IP?>/app/member/FT_<?=$browse?>/index.php?rtype=r&uid=<?=$uid?>&langx=<?=$langx?>&mtype=3&showtype=',parent.FT_lid_type,'SI2');chg_type_class('re_class');">独赢 ＆ 让球 ＆ 大小 & 单 / 双</a></li>
      <li class="pd"><a id="pd_class" class="type_out" href="javascript:void(0);" onClick="chg_button_bg('FT','<?=$button?>');chg_type('<?=BROWSER_IP?>/app/member/FT_<?=$browse?>/index.php?rtype=pd&uid=<?=$uid?>&langx=<?=$langx?>&mtype=3&showtype=',parent.FT_lid_type,'SI2');chg_type_class('pd_class');">波胆</a></li>
      <li class="to"><a id="to_class" class="type_out" href="javascript:void(0);" onClick="chg_button_bg('FT','<?=$button?>');chg_type('<?=BROWSER_IP?>/app/member/FT_<?=$browse?>/index.php?rtype=t&uid=<?=$uid?>&langx=<?=$langx?>&mtype=3&showtype=',parent.FT_lid_type,'SI2');chg_type_class('to_class');">总入球</a></li>
      <li class="hf"><a id="hf_class" class="type_out" href="javascript:void(0);" onClick="chg_button_bg('FT','<?=$button?>');chg_type('<?=BROWSER_IP?>/app/member/FT_<?=$browse?>/index.php?rtype=f&uid=<?=$uid?>&langx=<?=$langx?>&mtype=3&showtype=',parent.FT_lid_type,'SI2');chg_type_class('hf_class');">半场 / 全场</a></li>
      <li class="hp3"><a id="hp3_class" class="type_out" href="<?=BROWSER_IP?>/app/member/FT_<?=$browse?>/index.php?rtype=p3&uid=<?=$uid?>&langx=<?=$langx?>&mtype=3&showtype=" target="body" onClick="chg_button_bg('FT','<?=$button?>');chg_type_class('hp3_class');">综合过关</a></li>
      <li class="fs"><a id="fs_class" class="type_out" href="<?=BROWSER_IP?>/app/member/browse_FS/loadgame_R.php?uid=<?=$uid?>&langx=<?=$langx?>&FStype=FT&mtype=3" onClick="chg_button_bg('FT','today');parent.sel_league='';parent.sel_area='';chg_type_class('fs_class');"  target="body" >冠军</a></li>
   <?php if ($style=='FT'){
?>
    <li class="fs" style="display: none;"><a id="fs_class" class="type_out" href="<?=BROWSER_IP?>/live/live.php?uid=<?=$uid?>&langx=<?=$langx?>" onClick="javascript:document.getElementById('rb_btn').className='rb';document.getElementById('today_btn').className='today';document.getElementById('early_btn').className='early';document.getElementById('video_btn').className='early_on'; "  target="body" >真人娱乐</a></li>
      <?php
       }else {?>
      <li class="fs"><a id="fs_class" class="type_out" href="<?=BROWSER_IP?>/live/live.php?uid=<?=$uid?>&langx=<?=$langx?>" onClick="javascript:document.getElementById('rb_btn').className='rb';document.getElementById('today_btn').className='today';document.getElementById('early_btn').className='early';document.getElementById('video_btn').className='early_on'; "  target="body" >真人娱乐</a></li>
       <?php
        }
       ?>

            <li class="result"><a id="result_class" class="type_out" href="http://qm6686.com" target="_blank"><font color="#FF0000">足球比分</font></a></li>
            <li class="result"><a id="result_class" class="type_out" href="<?=BROWSER_IP?>/app/member/result/result.php?game_type=FT&uid=<?=$uid?>&langx=<?=$langx?>" target="body" onClick="chg_button_bg('FT','today');chg_type_class('result_class');">赛果</a></li>
            <li class="result"><a id="result_class" class="type_out" href="http://www.live6686.com" target="_blank">APP下载</a></li>
    </ul>
  </div>
  <!-- rb-->
  <div id="nav_re" style="display: none;">
    <ul class="level1">
      <li class="ft"><span class="ball"><a href="#" onclick="Go_RB_page('FT');chg_button_bg('FT','today');">足球 (<strong class="game_sum" id="RB_FT_games">0</strong>)</a></span></li>
      <li class="bk"><span class="ball"><a href="#" onclick="Go_RB_page('BK');chg_button_bg('BK','today');">篮球<span class="ball_nf"></span>美式足球 (<strong class="game_sum" id="RB_BK_games">1</strong>)</a></span></li>
      <li class="tn"><span class="ball"><a href="#" onclick="Go_RB_page('TN');chg_button_bg('TN','today');">网球 (<strong class="game_sum" id="RB_TN_games">4</strong>)</a></span></li>
            <li class="vb"><span class="ball"><a href="#" onClick="Go_RB_page('VB');chg_button_bg('VB','today');">排球 (<strong class="game_sum" id="RB_VB_games"></strong>)</a></span></li>
            <li class="ym"><span class="ball"><a href="#" onClick="Go_RB_page('BM');chg_button_bg('BM','today');">羽毛球 (<strong class="game_sum" id="RB_BM_games">0</strong>)</a></span></li>
            <li class="pp"><span class="ball"><a href="#" onClick="Go_RB_page('TT');chg_button_bg('TT','today');">乒乓球 (<strong class="game_sum" id="RB_TT_games">0</strong>)</a></span></li>
      <li class="bs"><span class="ball"><a href="#" onclick="Go_RB_page('BS');chg_button_bg('BS','today');">棒球 (<strong class="game_sum" id="RB_BS_games">0</strong>)</a></span></li>
	  <li class="sk"><span><a  style="padding:4px 10px !important" href="#" onclick="Go_RB_page('SK');chg_button_bg('SK','today');">斯诺克/台球 (<strong class="game_sum" id="RB_SK_games">0</strong>)</a></span></li>
      <li class="op"><span class="ball"><a href="#" onclick="Go_RB_page('OP');chg_button_bg('OP','today');">其他 (<strong class="game_sum" id="RB_OP_games">0</strong>)</a></span></li>
    </ul>
  </div>
  <div id="type_re" style="display: none;">
    <ul>
      <li class="re"><a id="rb_class" class="type_on" href="javascript:void(0);" onclick="chg_button_bg('FT','rb');chg_type('<?=BROWSER_IP?>/app/member/FT_browse/index.php?rtype=re&uid=<?=$uid?>&langx=<?=$langx?>&mtype=3&showtype=',parent.FT_lid_type,'SI2');chg_type_class('rb_class');">独赢 ＆ 让球 ＆ 大小 & 单 / 双</a></li>
      <li class="pd"><a id="rpd_class" class="type_out" href="javascript:void(0);" onclick="chg_button_bg('FT','rb');chg_type('<?=BROWSER_IP?>/app/member/FT_browse/index.php?rtype=rpd&uid=<?=$uid?>&langx=<?=$langx?>&mtype=3&showtype=',parent.FT_lid_type,'SI2');chg_type_class('rpd_class');">波胆</a></li>
      <li class="to"><a id="rto_class" class="type_out" href="javascript:void(0);" onclick="chg_button_bg('FT','rb');chg_type('<?=BROWSER_IP?>/app/member/FT_browse/index.php?rtype=rt&uid=<?=$uid?>&langx=<?=$langx?>&mtype=3&showtype=',parent.FT_lid_type,'SI2');chg_type_class('rto_class');">总入球</a></li>
      <li class="hf"><a id="rhf_class" class="type_out" href="javascript:void(0);" onclick="chg_button_bg('FT','rb');chg_type('<?=BROWSER_IP?>/app/member/FT_browse/index.php?rtype=rf&uid=<?=$uid?>&langx=<?=$langx?>&mtype=3&showtype=',parent.FT_lid_type,'SI2');chg_type_class('rhf_class');">半场 / 全场</a></li>
	  <!--
	  <li class="hp3"><a id="hp3_class" class="type_out" href="<?=BROWSER_IP?>/app/member/FT_<?=$browse?>/index.php?rtype=p3&uid=<?=$uid?>&langx=<?=$langx?>&mtype=3&showtype=" target="body" onClick="chg_button_bg('FT','<?=$button?>');chg_type_class('hp3_class');">综合过关</a></li>
      <li class="fs"><a id="fs_class" class="type_out" href="<?=BROWSER_IP?>/app/member/browse_FS/loadgame_R.php?uid=<?=$uid?>&langx=<?=$langx?>&FStype=FT&mtype=3" onClick="chg_button_bg('FT','today');parent.sel_league='';parent.sel_area='';chg_type_class('fs_class');"  target="body" >冠军</a></li>
     -->
	 <?php if ($style=='FT'){
?>
    <li class="fs" style="display: none;"><a id="fs_class" class="type_out" href="<?=BROWSER_IP?>/live/live.php?uid=<?=$uid?>&langx=<?=$langx?>" onClick="javascript:document.getElementById('rb_btn').className='rb';document.getElementById('today_btn').className='today';document.getElementById('early_btn').className='early';document.getElementById('video_btn').className='early_on'; "  target="body" >真人娱乐</a></li>
      <?php
       }else {?>
      <li class="fs"><a id="fs_class" class="type_out" href="<?=BROWSER_IP?>/live/live.php?uid=<?=$uid?>&langx=<?=$langx?>" onClick="javascript:document.getElementById('rb_btn').className='rb';document.getElementById('today_btn').className='today';document.getElementById('early_btn').className='early';document.getElementById('video_btn').className='early_on'; "  target="body" >真人娱乐</a></li>
       <?php
        }
       ?>

            <li class="result"><a id="result_class" class="type_out" href="http://qm6686.com" target="_blank"><font color="#FF0000">足球比分</font></a></li>
            <li class="result"><a id="result_class" class="type_out" href="<?=BROWSER_IP?>/app/member/result/result.php?game_type=FT&uid=<?=$uid?>&langx=<?=$langx?>" target="body" onClick="chg_button_bg('FT','today');chg_type_class('result_class');">赛果</a></li>
            <li class="result"><a id="result_class" class="type_out" href="http://www.live6686.com" target="_blank">APP下载</a></li>
    </ul>
   </div>
  <!-- rb end-->
</div>
<!--input  id=downloadBTN type=button style="width:80px;visibility:'hidden'"  onclick="onclickDown()" value="下载"-->
    <!--主选单-->
    <div id="back">
    	<ul>
<?
switch ($langx){
case 'zh-tw':
?>
          <li class="lang_top"><a href="#" id="lang_tw">繁體</a>
                <ul class="pd">
                    <li class="tw" onClick="OnMouseOverEvent();"><a href="javascript:void(0);" onClick="changeLangx('zh-tw')">繁體</a></li>
                    <li class="cn" onClick="OnMouseOverEvent();"><a href="javascript:void(0);" onClick="changeLangx('zh-cn')">简体</a></li>
                    <li class="us" onClick="OnMouseOverEvent();"><a href="javascript:void(0);" onClick="changeLangx('en-us')">English</a></li>
                </ul>
          </li>
<?
break;
case 'zh-cn':
?>
          <li class="lang_top"><a href="#" id="lang_cn">简体</a>
                <ul class="pd">
                    <li class="cn" onClick="OnMouseOverEvent();"><a href="javascript:void(0);" onClick="changeLangx('zh-cn')">简体</a></li>
                    <li class="tw" onClick="OnMouseOverEvent();"><a href="javascript:void(0);" onClick="changeLangx('zh-tw')">繁體</a></li>
                    <li class="us" onClick="OnMouseOverEvent();"><a href="javascript:void(0);" onClick="changeLangx('en-us')">English</a></li>
                </ul>
          </li>
<?
break;
case 'en-us':
?>
          <li class="lang_top"><a href="#" id="lang_en">English</a>
                <ul class="pd">
                    <li class="us" onClick="OnMouseOverEvent();"><a href="javascript:void(0);" onClick="changeLangx('en-us')">English</a></li>
                    <li class="tw" onClick="OnMouseOverEvent();"><a href="javascript:void(0);" onClick="changeLangx('zh-tw')">繁體</a></li>
                    <li class="cn" onClick="OnMouseOverEvent();"><a href="javascript:void(0);" onClick="changeLangx('zh-cn')">简体</a></li>
                </ul>
          </li>
<?
break;
}
?>

           <li class="mail" onClick="OnMouseOverEvent();"><a href="#" id="chg_pwd" onClick="Go_Chg_pass();" style="cursor:hand">更改密码</a></li>
           <li class="qa" onClick="OnMouseOverEvent();"><a href="#">帮助</a><!--[if IE 7]><!--><!--<![endif]-->
			<!--[if lte IE 6]><table><tr><td><![endif]-->

                <ul class="pd">
                       <li class="qa_on"><a href="#">帮助</a></li>
                       <li class="msg"><a href="#" onClick="parent.mem_order.showMoreMsg();">公告栏</a></li>
                       <li class="roul"><a href="#" OnClick="window.open('/tpl/member/<?=$langx?>/QA_sport.html','QA','location=no,status=no,width=800,height=428,toolbar=no,top=0,left=0,scrollbars=yes,resizable=yes,personalbar=yes');">体育规则</a></li>
                       <li class="wap"><a href="#" OnClick="window.open('/tpl/member/<?=$langx?>/roul_mp.html','WAP','location=no,status=no,width=680,height=500,toolbar=no,top=0,left=0,scrollbars=no,resizable=no,personalbar=yes');">Wap指南</a></li>
                       <!-- <li class="odd"><a href="#" OnClick="window.open('/tpl/member/<?=$langx?>/QA_way.html','QA','location=no,status=no,width=800,height=428,toolbar=no,top=0,left=0,scrollbars=yes,resizable=yes,personalbar=yes');">赔率计算列表</a></li> -->
                       <!--<li class="odd"><a  href="<?=BROWSER_IP?>/tpl/member/<?=$langx?>/QA_way.html" target="body">详细设定</a></li>-->
					   <li class="odd"><a href="#" OnClick="window.open('/tpl/member/<?=$langx?>/QA_way.html','QA','location=no,status=no,width=800,height=428,toolbar=no,top=0,left=0,scrollbars=yes,resizable=yes,personalbar=yes');">详细设定</a></li>
         	 </ul>
             <!--[if lte IE 6]></td></tr></table></a><![endif]-->
          </li>
   		  <li class="home" onMouseOver="OnMouseOutEvent()"><a href="<?=BROWSER_IP?>/app/member/logout.php?uid=<?=$uid?>&langx=<?=$langx?>" target="_top">退出</a></li>
	  </ul>
  </div>

<div id="mem_box">
    <div id="mem_main"><span class="his"><a href="<?=BROWSER_IP?>/app/member/history/history_data.php?uid=<?=$uid?>&langx=<?=$langx?>" target="body">帐户历史</a></span> | <span class="wag"><a href="<?=BROWSER_IP?>/app/member/today/today_wagers.php?uid=<?=$uid?>&langx=<?=$langx?>" target="body">交易状况（<span class="scanOrder"><?=$ncou?$ncou:0?></span>）</a></span></div>
  <div id="credit_main"><span id="credit">&nbsp;</span>&nbsp;<!--视讯：<span id="cag">0</span>--><input name="" type="button" class="re_credit" value="" onClick="javascript:reloadCrditFunction();"></div>
</div>

<div id="extra2"><a href="http://live228.com/app/member/mem_add.php?langx=<?=$langx?>" target="_blank"></a></div>
<iframe id="reloadPHP" name="reloadPHP"  width="0" height="0"></iframe>
<iframe id="reloadPHP" name="reloadPHP1"  width="0" height="0"></iframe>
<iframe id=memOnline name=memOnline  width=0 height=0></iframe>
</body>
</html>
<script language="JavaScript" src="/js/jquery-1.9.1.min.js"></script> 
<script language="javascript">

    $(function(){
        var width=window.screen.height;
        var height=window.screen.width;
$.ajax({
    type: "GET",
    url: "/app/member/clientinfo_service.php",
    data: {uid:"<? echo $uid;?>",width:width,height:height},
    dataType: "json",
    success: function(data){

    }
    });

 });

function getUnreadNum(){
	//获取消息唯读条数
    $.ajax({
        type: "GET",
        url: "/app/member/Message/unreadnum.php",
        data: {uid:"<? echo $uid;?>"},
        dataType: "json",
        success: function(ret){
             $("#messagenum").html(ret);
        }
    });
}

$(function(){
	setInterval("getUnreadNum()",30000);
});

function getOrderNum(){
	$.ajax({
        type: "GET",
        url: "/app/member/today/ordernum.php",
        data: {uid:"<? echo $uid;?>"},
        success: function(ret){
			ret=0;
            $(".scanOrder").html(ret);
        }
    });
}
$(function(){
	getOrderNum();
	setInterval('getOrderNum()',30000);
})
</script>
