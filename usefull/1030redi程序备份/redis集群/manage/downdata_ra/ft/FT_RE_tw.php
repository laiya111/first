<?
$t1 = microtime(true);
require ("../../include/config.inc.php");
require ("../../include/curl_http.php");
require ("../../include/redis.conf.php");
$mysql = "select DataSite_tw,Uid_tw,udp_ft_re from web_system_data where ID=1";
$result = mysql_query($mysql);
$row = mysql_fetch_array($result);
$uid = $row['Uid_tw'];
$site = $row['DataSite_tw'];
$settime = $row['udp_ft_re'];
//正式上线，注释以下语句
//$settime =;
$allcount = 0;

$curl = &new Curl_HTTP_Client();
$curl->store_cookies("cookies.txt");
$curl->set_user_agent("Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36");
$html_data = $curl->fetch_url("" . $site . "/app/member/FT_browse/index.php?rtype=re&uid=$uid&langx=zh-tw&mtype=4");
$html_data = $curl->fetch_url("" . $site . "/app/member/FT_browse/body_var.php?uid=$uid&rtype=re&langx=zh-tw&mtype=4&page_no=0");
// var_dump($html_data);
function explode_arr(&$arr)
{
    foreach ($arr as $key => $val) {
        $val = str_replace("'", "", $val);
        $arr[$key] = explode(',', $val);
    }
}

preg_match_all("/od\('\d+',\[(.+?)\]\);/is", $html_data, $newms);
// preg_match_all("/od\('(\d+?)',\[(.+?)\]\);/is",$html_data,$newms);
$newmatch = $newms[1];
explode_arr($newmatch);
$i = 0;
$sql = "";
foreach ($newmatch as $datainfo) {
    if (! empty($datainfo)) {
        if ($i ++ == 0) {
            $sql = "insert into match_sports(mid,mb1tg0,mb2tg0,mb2tg1,mb3tg0,mb3tg1,mb3tg2,mb4tg0,mb4tg1,mb4tg2,mb4tg3,mb0tg0,mb1tg1,mb2tg2,mb3tg3,mb4tg4,up5,mb0tg1,mb0tg2,mb1tg2,mb0tg3,mb1tg3,mb2tg3,mb0tg4,mb1tg4,mb2tg4,mb3tg4) 
	        values('{$datainfo[0]}','{$datainfo[8]}','{$datainfo[9]}','{$datainfo[10]}','{$datainfo[11]}','{$datainfo[12]}','{$datainfo[13]}','{$datainfo[14]}','{$datainfo[15]}','{$datainfo[16]}','{$datainfo[17]}','{$datainfo[18]}',
	        '{$datainfo[19]}','{$datainfo[20]}','{$datainfo[21]}','{$datainfo[22]}','{$datainfo[23]}','{$datainfo[24]}','{$datainfo[25]}','{$datainfo[26]}','{$datainfo[27]}','{$datainfo[28]}','{$datainfo[29]}','{$datainfo[30]}',
	        '{$datainfo[31]}','{$datainfo[32]}','{$datainfo[33]}') ";
        } else {
            $sql .= ",('{$datainfo[0]}','{$datainfo[8]}','{$datainfo[9]}','{$datainfo[10]}','{$datainfo[11]}','{$datainfo[12]}','{$datainfo[13]}','{$datainfo[14]}','{$datainfo[15]}','{$datainfo[16]}','{$datainfo[17]}','{$datainfo[18]}',
	        '{$datainfo[19]}','{$datainfo[20]}','{$datainfo[21]}','{$datainfo[22]}','{$datainfo[23]}','{$datainfo[24]}','{$datainfo[25]}','{$datainfo[26]}','{$datainfo[27]}','{$datainfo[28]}','{$datainfo[29]}','{$datainfo[30]}',
	        '{$datainfo[31]}','{$datainfo[32]}','{$datainfo[33]}')";
        }
    }
}
if (! empty($sql)) {
    $sql .= " on duplicate key update mb1tg0=values(mb1tg0),mb2tg0=values(mb2tg0),mb2tg1=values(mb2tg1),mb3tg0=values(mb3tg0),mb3tg1=values(mb3tg1),mb3tg2=values(mb3tg2),mb4tg0=values(mb4tg0),mb4tg1=values(mb4tg1),
        mb4tg2=values(mb4tg2),mb4tg3=values(mb4tg3),mb0tg0=values(mb0tg0),mb1tg1=values(mb1tg1),mb2tg2=values(mb2tg2),mb3tg3=values(mb3tg3),mb4tg4=values(mb4tg4),up5=values(up5),mb0tg1=values(mb0tg1),mb0tg2=values(mb0tg2),
        mb1tg2=values(mb1tg2),mb0tg3=values(mb0tg3),mb1tg3=values(mb1tg3),mb2tg3=values(mb2tg3),mb0tg4=values(mb0tg4),mb1tg4=values(mb1tg4),mb2tg4=values(mb2tg4),mb3tg4=values(mb3tg4)";
    mysql_query($sql) or die("操作失敗!");
}

preg_match_all("/g\(\[(.+?)\]\);/is", $html_data, $oldms);
$oldmatch = $oldms[1];
explode_arr($oldmatch);
$i = 0;
$sql = "";
$sql_array = array();
$match_scarr = array();
foreach ($oldmatch as $datainfo) {
    // 有TEST 不要加入其它的页面应该也加入以下功能
    if(stripos($datainfo[5], 'TEST') !== false||stripos($datainfo[6], 'TEST') !== false)
    {
        continue;
    }
    if ($datainfo[47] == '') {
        $aa = $datainfo[42];
    } else {
        $aa = $datainfo[47];
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
    if ($i ++ % 100 == 0) {
        if (strlen($sql) > 0) {
            $sql_array[] = $sql . " on duplicate key update M_Date=values(M_Date),M_Time=values(M_Time),M_Start=values(M_Start),MB_Team_tw=values(MB_Team_tw),TG_Team_tw=values(TG_Team_tw),
	            M_League_tw=values(M_League_tw),MB_MID=values(MB_MID),TG_MID=values(TG_MID),RB_Show=values(RB_Show),ShowTypeRB=values(ShowTypeRB),M_LetB_RB=values(M_LetB_RB),
	            MB_LetB_Rate_RB=values(MB_LetB_Rate_RB),TG_LetB_Rate_RB=values(TG_LetB_Rate_RB),MB_Dime_RB=values(MB_Dime_RB),TG_Dime_RB=values(TG_Dime_RB),MB_Dime_Rate_RB=values(MB_Dime_Rate_RB),
	            TG_Dime_Rate_RB=values(TG_Dime_Rate_RB),ShowTypeHRB=values(ShowTypeHRB),M_LetB_RB_H=values(M_LetB_RB_H),MB_LetB_Rate_RB_H=values(MB_LetB_Rate_RB_H),
	            TG_LetB_Rate_RB_H=values(TG_LetB_Rate_RB_H),MB_Dime_RB_H=values(MB_Dime_RB_H),TG_Dime_RB_H=values(TG_Dime_RB_H),MB_Dime_Rate_RB_H=values(MB_Dime_Rate_RB_H),
	            TG_Dime_Rate_RB_H=values(TG_Dime_Rate_RB_H),mb_win_rate_rb=values(mb_win_rate_rb),tg_win_rate_rb=values(tg_win_rate_rb),m_flat_rate_rb=values(m_flat_rate_rb),
                mb_win_rate_rb_h=values(mb_win_rate_rb_h),tg_win_rate_rb_h=values(tg_win_rate_rb_h),m_flat_rate_rb_h=values(m_flat_rate_rb_h),s_single_rate=values(s_single_rate),
                s_double_rate=values(s_double_rate),eventid=values(eventid),hot=values(hot),play=values(play),s_show=values(s_show),now_play=values(now_play),crown_order=values(crown_order)";
        }
        $sql = "";
        $sql .= "insert into match_sports(MID,Type,M_Date,M_Time,M_Start,MB_Team_tw,TG_Team_tw,M_League_tw,MB_MID,TG_MID,RB_Show,ShowTypeRB,M_LetB_RB,MB_LetB_Rate_RB,
	    TG_LetB_Rate_RB,MB_Dime_RB,TG_Dime_RB,MB_Dime_Rate_RB,TG_Dime_Rate_RB,ShowTypeHRB,M_LetB_RB_H,MB_LetB_Rate_RB_H,TG_LetB_Rate_RB_H,MB_Dime_RB_H,TG_Dime_RB_H,
	    MB_Dime_Rate_RB_H,TG_Dime_Rate_RB_H,mb_win_rate_rb,tg_win_rate_rb,m_flat_rate_rb,mb_win_rate_rb_h,tg_win_rate_rb_h,m_flat_rate_rb_h,s_single_rate,s_double_rate,
	    eventid,hot,play,s_show,now_play,crown_order) 
	        values('{$datainfo[0]}','FT','{$m_date}','{$m_time}','{$timestamp}','{$datainfo[5]}','{$datainfo[6]}','{$datainfo[2]}','{$datainfo[3]}','{$datainfo[4]}','1',
	    '{$datainfo[7]}','{$datainfo[8]}','{$datainfo[9]}','{$datainfo[10]}','{$datainfo[11]}','{$datainfo[12]}','{$datainfo[14]}','{$datainfo[13]}','{$datainfo[21]}','{$datainfo[22]}',
	        '{$datainfo[23]}','{$datainfo[24]}','{$datainfo[25]}','{$datainfo[26]}','{$datainfo[28]}','{$datainfo[27]}','$datainfo[33]','$datainfo[34]','$datainfo[35]','$datainfo[36]',
	    '$datainfo[37]','$datainfo[38]','$datainfo[41]','$datainfo[42]','$datainfo[43]','$datainfo[44]','$datainfo[46]',0,'" . strip_tags($datainfo[1]) . "||" . strip_tags($datainfo[48]) . "',{$i})";
    } else {
        $sql .= ",('{$datainfo[0]}','FT','{$m_date}','{$m_time}','{$timestamp}','{$datainfo[5]}','{$datainfo[6]}','{$datainfo[2]}','{$datainfo[3]}','{$datainfo[4]}','1',
	    '{$datainfo[7]}','{$datainfo[8]}','{$datainfo[9]}','{$datainfo[10]}','{$datainfo[11]}','{$datainfo[12]}','{$datainfo[14]}','{$datainfo[13]}','{$datainfo[21]}','{$datainfo[22]}',
	        '{$datainfo[23]}','{$datainfo[24]}','{$datainfo[25]}','{$datainfo[26]}','{$datainfo[28]}','{$datainfo[27]}','$datainfo[33]','$datainfo[34]','$datainfo[35]','$datainfo[36]',
	    '$datainfo[37]','$datainfo[38]','$datainfo[41]','$datainfo[42]','$datainfo[43]','$datainfo[44]','$datainfo[46]',0,'" . strip_tags($datainfo[1]) . "||" . strip_tags($datainfo[48]) . "',{$i})";
    }
    $match_scarr["{$datainfo[0]}"] = array(
        'mb_team' => $datainfo[5],
        'tg_team' => $datainfo[6],
        'mb_score' => $datainfo[18],
        'tg_score' => $datainfo[19],
        'mb_card' => $datainfo[29],
        'tg_card' => $datainfo[30],
        'm_start' => $timestamp,
        'now_play' => strip_tags($datainfo[1]) . "||" . strip_tags($datainfo[48])
    );
    $allcount ++;
}
if (!empty($sql)) {
    $sql_array[] = $sql. " on duplicate key update M_Date=values(M_Date),M_Time=values(M_Time),M_Start=values(M_Start),MB_Team_tw=values(MB_Team_tw),TG_Team_tw=values(TG_Team_tw),
	            M_League_tw=values(M_League_tw),MB_MID=values(MB_MID),TG_MID=values(TG_MID),RB_Show=values(RB_Show),ShowTypeRB=values(ShowTypeRB),M_LetB_RB=values(M_LetB_RB),
	            MB_LetB_Rate_RB=values(MB_LetB_Rate_RB),TG_LetB_Rate_RB=values(TG_LetB_Rate_RB),MB_Dime_RB=values(MB_Dime_RB),TG_Dime_RB=values(TG_Dime_RB),MB_Dime_Rate_RB=values(MB_Dime_Rate_RB),
	            TG_Dime_Rate_RB=values(TG_Dime_Rate_RB),ShowTypeHRB=values(ShowTypeHRB),M_LetB_RB_H=values(M_LetB_RB_H),MB_LetB_Rate_RB_H=values(MB_LetB_Rate_RB_H),
	            TG_LetB_Rate_RB_H=values(TG_LetB_Rate_RB_H),MB_Dime_RB_H=values(MB_Dime_RB_H),TG_Dime_RB_H=values(TG_Dime_RB_H),MB_Dime_Rate_RB_H=values(MB_Dime_Rate_RB_H),
	            TG_Dime_Rate_RB_H=values(TG_Dime_Rate_RB_H),mb_win_rate_rb=values(mb_win_rate_rb),tg_win_rate_rb=values(tg_win_rate_rb),m_flat_rate_rb=values(m_flat_rate_rb),
                mb_win_rate_rb_h=values(mb_win_rate_rb_h),tg_win_rate_rb_h=values(tg_win_rate_rb_h),m_flat_rate_rb_h=values(m_flat_rate_rb_h),s_single_rate=values(s_single_rate),
                s_double_rate=values(s_double_rate),eventid=values(eventid),hot=values(hot),play=values(play),s_show=values(s_show),now_play=values(now_play),crown_order=values(crown_order)";
}
$mids = array();
$mids = array_map('array_shift', $oldmatch);
if (count($oldmatch) > 0) {
    // 特殊情况下没有开的，自动扫一次开启
    //$sql_array[] = "update `match_sports` tb1,tmp_match_race tb2 set overdue=1,overdue_time=null where tb1.RB_Show=1 and tb1.type='FT' and tb1.mid=tb2.mid and tb1.overdue=0 and tb1.overdue_time<=now()+interval -150 second";
	$sqloverdue = "select MID from match_sports where RB_Show=1 and type='FT' and M_Date = '$m_date'";
	$result = mysql_query($sqloverdue);
	//$redis->select(1);
	while($rowMid =  mysql_fetch_assoc($result)){
		$overduearr = json_decode($redis->get($rowMid['MID']),true);
		if($overduearr['overdue']==0 && $overduearr['overdue_time']<=date('Y-m-d H:i:s',time()-150)){
			$oarr = array(
				"overdue"=>1,
				"overdue_time"=>''
			);
			$redis->set($rowMid['MID'],json_encode($oarr));
		}
	};
    $sql_array[] = "update `match_sports` set RB_Show=0 where RB_Show=1 and type='FT' and mid not in(" . implode(',', $mids) . ") ";
} else {
    $sql_array[] = "update `match_sports` set RB_Show=0 where RB_Show=1 and type='FT'";
}
foreach ($sql_array as $sql_row)
{
    //echo $sql_row."<br>";
    mysql_query($sql_row);

}
//更新比分
if(count($oldmatch)>0)
{
    $sql = "select tb1.mid,ifnull(b.hg_hscore,0)hg_hscore,ifnull(b.hg_ascore,0)hg_ascore,b.mb_ball,b.tg_ball from  match_sports tb1 left join match_score_rec b on tb1.mid = b.mid where tb1.RB_Show=1 and tb1.type='FT'";
    //echo $sql;
    $score_values = array();
    $res = mysql_query($sql);
    while ($o_row = mysql_fetch_assoc($res)) {
        $o_mid = $o_row['mid'];
        $score_row=$match_scarr["{$o_mid}"];
        if ($score_row['mb_score'] != '' && $score_row['tg_score'] != '') {
            // echo "ggyy<br>";
            if ($score_row['mb_score'] != $o_row['hg_hscore'] || $score_row['tg_score'] != $o_row['hg_ascore']) {
                $hg_action = - 1;
                if ((int) $score_row['mb_score'] > (int) $o_row['hg_hscore'] || (int) $score_row['tg_score'] > (int) $o_row['hg_ascore']) {
                    $hg_action = 0;
                } else 
                    if ((int) $score_row['mb_score'] == ((int) $o_row['hg_hscore'] - 1) || (int) $score_row['tg_score'] == ((int) $o_row['hg_ascore'] - 1)) {
                        $hg_action = 1;
                    }
                if ($hg_action > - 1) {
                    $score_values[] = "($o_mid,'{$score_row['mb_score']}','{$score_row['tg_score']}',now(),'{$hg_action}','1','{$score_row['mb_score']}','{$score_row['tg_score']}')";
                }
            }
        }
    }
    if(count($score_values)>0)
    {
        $sql="insert into match_score_rec(mid,hg_hscore,hg_ascore,hg_time,hg_action,last_from,mb_ball,tg_ball) values".implode(",", $score_values)." on duplicate key update hg_hscore=values(hg_hscore),hg_ascore=values(hg_ascore),hg_time=values(hg_time),hg_action=values(hg_action),last_from=values(last_from)";
        mysql_query($sql) or die("提交比分: " . mysql_error());
    }
}
// 更新牌
if (count($oldmatch) > 0){
    $sql_values = array();
    $sql = "select tb1.mid,tb1.mb_score,tb1.tg_score from score_change_ft tb1, (SELECT max(a.id) id,a.mid FROM score_change_ft a,match_sports b WHERE a.mid = b.mid AND b.RB_Show = 1 AND b.Type = 'FT' AND a.ball_type = '1' GROUP BY a.mid )tb2 where tb1.id = tb2.id ";
    //echo $sql . "<br>";
    $res = mysql_query($sql);
    $my_score_array = array();
    while ($rs = mysql_fetch_assoc($res)) {
        $mid = $rs['mid'];
        $my_score_array["{$mid}"] = $rs;
    }
    $sql = "";
    $sql_values = array();
    foreach ($match_scarr as $key => $match_row) {
        $gtype="0";
        if (((int) $match_row['mb_card'] > (int) $my_score_array["{$key}"]["mb_score"]) || ((int) $match_row['tg_card'] > (int) $my_score_array["{$key}"]["tg_score"])) {
            if((int) $match_row['mb_card'] > (int) $my_score_array["{$key}"]["mb_score"]&&(int) $match_row['tg_card'] > (int) $my_score_array["{$key}"]["tg_score"])
            {
                $gtype="2";
            }
            else if((int) $match_row['tg_card'] > (int) $my_score_array["{$key}"]["tg_score"])
            {
                $gtype="1";
            }
            $sql_values[] = "($key,'{$match_row['mb_team']}','{$match_row['tg_team']}','{$match_row['mb_card']}','{$match_row['tg_card']}',now(),'{$match_row['m_start']}','{$match_row['now_play']}','1','{$gtype}','HG','0')";         
        }
    }
    if (count($sql_values) > 0) {
        $sql .= "insert into score_change_ft(mid,mb_team,tg_team,mb_score,tg_score,m_time,m_start,now_play,ball_type,gtype,last_from,sc_action) values " . implode(",", $sql_values);
        //echo $sql;
        mysql_query($sql);
    }
}
mysql_close();
$t2 = microtime(true);
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title></title>
<link href="/style/agents/control_down.css" rel="stylesheet" type="text/css">
</head>
<body>
<script> 
<!-- 
var limit="<?=$settime?>" 
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
		curtime=curmin+"秒後自動獲取!" 
	else 
		curtime=cursec+"秒後自動獲取!" 
		timeinfo.innerText=curtime 
		setTimeout("beginrefresh()",1000) 
	} 
} 

window.onload=beginrefresh 
//file://--> 
</script>
<table width="100" height="70" border="0" cellpadding="0" cellspacing="0">
  <tr> 
    <td width="100" height="70" align="center"><?='耗时'.round($t2-$t1,3).'秒'?><br>走地數據接收<br><span id="timeinfo"></span><br><input type=button name=button value="繁体 <?=$allcount?>" onClick="window.location.reload()"></td>
  </tr>
</table>
</body>
</html>
