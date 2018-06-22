<?php
header("Expires: Mon, 26 Jul 1970 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Content-type: text/html; charset=utf-8");
$isOpenFilter=1;
require ("../../agents/include/config.inc.php");
require ("../../agents/include/redis.conf.php");
require ("../../agents/include/config.slave.write.php");
include ("curl_http.php");

$rb_data = stripslashes($_REQUEST['rb_data']);
$scrb_data = stripslashes($_REQUEST['scrb_data']);

$rb_data_array = json_decode($rb_data, true);
$scrb_data_array = json_decode($scrb_data, true);

//sim 每次有事件过来,将篮球赛事缓存或更新
$redis->select(0);
$pbkey = "bk".date('Y-m-d',strtotime("-1 day"));
if($redis->exists($pbkey)){
    $redis->del($pbkey);
}
//sim 每次有事件过来,判断之前的key是否过期，如果过期重新缓存 
$bkey = "bk".date('Y-m-d');
if(!$redis->exists($bkey)){
    $getThreePointSql = "select MID,MB_Team,TG_Team,m_league_en,mb_team_en,tg_team_en,m_date from match_sports where Type='BK' and m_date>'".date('Y-m-d',strtotime("-2 days"))."'";
    $getThreePointMidResult = mysql_query($getThreePointSql,$master);
    while($getThreePointMidRow = mysql_fetch_assoc($getThreePointMidResult)){
        $bkmatches[$getThreePointMidRow['MID']] = $getThreePointMidRow;
    }

    $redis->set($bkey,json_encode($bkmatches));
    $redis->expire($bkey,30);
}


$pfkey = "ft".date('Y-m-d',strtotime("-1 day"));
if($redis->exists($pfkey)){
    $redis->del($pfkey);
}

$fkey = "ft".date('Y-m-d');
if(!$redis->exists($fkey)){
    $selectMIDSql = "select MID,overdue,m_league_en,mb_team,tg_team,mb_team_en,tg_team_en,m_date,rb_show from match_sports where m_date>'".date('Y-m-d',strtotime("-2 days"))."' and Type='FT' and RB_Show=1";
    $ftresult=mysql_query($selectMIDSql);
    ;
    while ($ftrow=mysql_fetch_assoc($ftresult)) {
        $ftmatches[$ftrow['MID']] = $ftrow;
    }

    $redis->set($fkey,json_encode($ftmatches));
    $redis->expire($fkey,30);
}

if (count($rb_data_array) > 0) {
    foreach ($rb_data_array as $keys => $rb_row) {
		/**
		*sim
		*根据篮球事件更新篮球比分
		*@table rb_bk_score
		**/
		$bk = $rb_row['bk'];
		if($bk == '1'){
			//3分球记录，每进一个3分球相应记录加1
			
            if($redis->exists($bkey)){
                $matchs = json_decode($redis->get($bkey));
            }

            foreach ($matchs as $mid => $match) {
                if(strstr($rb_row['ml'],"NBA")){//只要是NAB的赛事，搜寻是否有总三分球盘口
                    if(strpos($match->m_league_en,"NBA")!==false && strpos($match->mb_team_en,"-")!==false && strpos(substr($match->mb_team_en,0,strpos($match->mb_team_en,"-")),$rb_row['mt'])!==false && strpos($match->tg_team_en,"-")!==false && strpos(substr($match->tg_team_en,0,strpos($match->tg_team_en,"-")),$rb_row['tt'])!==false && $match->m_date==$rb_row['d']){
                        if(strstr($match->MB_Team,"总3分球数")){
                            $bk_mid = $match->MID;
                            $bk_q_sql = "select count(1) as num from rb_bk_score where mid='$bk_mid'";
                            $bk_q_result = mysql_query($bk_q_sql,$master);
                            $bk_q_row = mysql_fetch_assoc($bk_q_result);
                            $bk_num = $bk_q_row["num"];
                            if($rb_row['e']=="1094"){//无法隔离普通盘口和3分球盘口
                                $bk_i_sql = "insert into rb_bk_score(game_id,mid,score_home,score_away,sourceType,M_Date) values('$keys','$bk_mid','1','0','rb','".$rb_row['d']."')";
                                $bk_u_sql = "update rb_bk_score set score_home=score_home+1 where mid = '$bk_mid'";
                            }elseif($rb_row['e']=="1113"){
                                $bk_u_sql = "update rb_bk_score set score_home=score_home-1 where mid = '$bk_mid'";
                            }elseif($rb_row['e']=="2118"){
                                $bk_i_sql = "insert into rb_bk_score(game_id,mid,score_home,score_away,sourceType,M_Date) values('$keys','$bk_mid','0','1','rb','".$rb_row['d']."')";
                                $bk_u_sql = "update rb_bk_score set score_away=score_away+1 where mid = '$bk_mid'";
                            }elseif($rb_row['e']=="2137"){
                                $bk_u_sql = "update rb_bk_score set score_away=score_away-1 where mid = '$bk_mid'";
                            }
                            $bk_num?mysql_query($bk_u_sql,$master):mysql_query($bk_i_sql,$master);
                        }
                    }
                }

                if($match->m_league_en==$rb_row['ml'] && (strpos($match->mb_team_en,$rb_row['mt'])!==false || strpos($rb_row['mt'],$match->mb_team_en)!==false) && (strpos($match->tg_team_en,$rb_row['tt'])!==false || strpos($rb_row['tt'],$match->tg_team_en)!==false) && $match->m_date==$rb_row['d']){                     
                    $bk_mid = $mid;
                    $bk_q_sql = "select count(1) as num from rb_bk_score where mid='$bk_mid'";
                    $bk_q_result = mysql_query($bk_q_sql,$master);
                    $bk_q_row = mysql_fetch_assoc($bk_q_result);
                    $bk_num = $bk_q_row["num"];
                    //查看此比分inplay有没有先写入
                    $rb_query_score = "select count(1) as rnum from rb_bk_score where mid='$bk_mid' and sourceType='inplay' and score_home='".$rb_row['score_home']."' and score_away='".$rb_row['score_away']."' and M_Date = '".$rb_row['d']."'";
                    $rb_query_ret = mysql_query($rb_query_score,$master);
                    $rb_query_row = mysql_fetch_assoc($rb_query_ret);
                    $rb_num = $rb_query_row["rnum"];
                    if($rb_num>0) continue;//如果比分已由inplay写入，则不再写入
                    $bk_i_sql = "insert into rb_bk_score(game_id,mid,score_home,score_away,sourceType,M_Date) values('$keys','$bk_mid','".$rb_row['score_home']."','".$rb_row['score_away']."','rb','".$rb_row['d']."')";
                    $bk_u_sql = "update rb_bk_score set score_home='".$rb_row['score_home']."',score_away='".$rb_row['score_away']."',sourceType='rb' where mid = '$bk_mid'";
                    $bk_num?mysql_query($bk_u_sql,$master):mysql_query($bk_i_sql,$master);
                    //推送
                    send('BK',0,$rb_row['score_home'],$rb_row['score_away'],$bk_mid,'');
                }
			}

			$bk_l_sql = "insert into rb_bk_log(game_id,event_code_id,event_code,m_date,m_league_en,mb_team_en,tg_team_en) values('" . $keys . "','" . $rb_row['e'] . "','" . $rb_row['ec'] . "','" . $rb_row['d'] . "','" . $rb_row['ml'] . "','" . $rb_row['mt'] . "','" . $rb_row['tt'] . "')";
			mysql_query($bk_l_sql,$master);

		}else{
            if($redis->exists($fkey)){
                $matchs = json_decode($redis->get($fkey));
            }
            $t = $rb_row['t'];
            //redis缓存overdue赛事封盘
            $redis->select(1);
            
            foreach ($matchs as $mid => $match) {
                if ($t == '0') {
                    if($match->m_league_en==$rb_row['ml'] && $match->mb_team_en==$rb_row['mt'] && $match->tg_team_en==$rb_row['tt'] && $match->m_date==$rb_row['d']){
                        $overduearr = array(
                            "game_id" => $keys,
                            "overdue" => $rb_row['o'],
                            "overdue_time" => date("Y-m-d H:i:s")
                        );
                        $redis->set($mid,json_encode($overduearr));
						//推送
						send('FT',$rb_row['o'],0,0,$mid,'');
                    }
                }elseif ($t == '1') {
                    if($match->m_league_en==$rb_row['ml'] && (strpos($match->mb_team_en,$rb_row['mt'])!==false || strpos($rb_row['mt'],$match->mb_team_en)!==false) && (strpos($match->tg_team_en,$rb_row['tt'])!==false || strpos($rb_row['tt'],$match->tg_team_en)!==false) && strpos($match->mb_team,'角球数')!==false && $match->m_date==$rb_row['d']){
                        $overduearr = array(
                            "game_id" => $keys,
                            "overdue" => $rb_row['o'],
                            "overdue_time" => date("Y-m-d H:i:s")
                        );
                        $redis->set($mid,json_encode($overduearr));
						//推送
						send('FT',$rb_row['o'],0,0,$mid,'');
                    }
                }
            }
        }
    }
}

if(count($scrb_data_array)>0){
    $tmp_match_score_array = array();
    $values_array = array();
    foreach ($scrb_data_array as $keys => $score_row) {
        if($score_row['sh']!=''&&$score_row['sa']!=''){
            $tmp_match_score_array[$keys]=array(
                "game_id"=>$keys,
                "mb_team_en"=>$score_row['mt'],
                "tg_team_en"=>$score_row['tt'],
                "m_league_en"=>$score_row['ml'],
                "m_date"=>$score_row['d'],
                "score_home"=>$score_row['sh'],
                "score_away"=>$score_row['sa'],
                "trb_action"=>$score_row['o'],
                "event_code_id"=>$score_row['e'],
                "event_code"=>$score_row['ec']
            );
        }
    }

    $log_values=array();

    $redis->select(0);
    if($redis->exists($fkey)){
        $matchs = json_decode($redis->get($fkey),true);
    }

    $match_sports_arr = array();
    foreach ($matchs as $mid => $match) {
        $match_sports_arr[$match['m_date']][$match['m_league_en']][$match['mb_team_en']][$match['tg_team_en']][]=$mid;
    }

    foreach ($tmp_match_score_array as $game_id => $tmp_match_score) {
        $midarr = $match_sports_arr[$tmp_match_score['m_date']][$tmp_match_score['m_league_en']][$tmp_match_score['mb_team_en']][$tmp_match_score['tg_team_en']];

        if(count($midarr)>0){
            foreach ($midarr as $mid) {
                $t_game_id=$game_id;
                $datetime = date('Y-m-d H:i:s');
                $values_array[] ="({$mid},'{$tmp_match_score['score_home']}','{$tmp_match_score['score_away']}','{$datetime}','{$tmp_match_score['trb_action']}','0','{$tmp_match_score['score_home']}','{$tmp_match_score['score_away']}')";
            }

            if(count($values_array)>0){
                $sql="insert into match_score_rec(mid,rb_hscore,rb_ascore,rb_time,rb_action,last_from,mb_ball,tg_ball) values " .implode(",",$values_array)." on duplicate key update rb_hscore=values(rb_hscore),rb_ascore=values(rb_ascore),rb_time=values(rb_time),rb_action=values(rb_action),last_from=values(last_from)";
                mysql_query($sql, $master);
            }
        }
    }
}

echo json_encode(getretMessage());
function getretMessage()
{
    return array(
        'success' => true,
    );
}

function getclientip()
{
    static $realip = NULL;

    if ($realip !== NULL) {
        return $realip;
    }
    if (isset($_SERVER)) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            foreach ($arr as $ip) {
                $ip = trim($ip);
                if ($ip != 'unknown') {
                    $realip = $ip;
                    break;
                }
            }
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $realip = $_SERVER['HTTP_CLIENT_IP'];
        } else {
            if (isset($_SERVER['REMOTE_ADDR'])) {
                $realip = $_SERVER['REMOTE_ADDR'];
            } else {
                $realip = '0.0.0.0';
            }
        }
    } else {
        if (getenv('HTTP_X_FORWARDED_FOR')) {
            $realip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_CLIENT_IP')) {
            $realip = getenv('HTTP_CLIENT_IP');
        } else {
            $realip = getenv('REMOTE_ADDR');
        }
    }
    preg_match("/[\d\.]{7,15}/", $realip, $onlineip);
    $realip = ! empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0';
    return $realip;
}

/**
 * @param $type 項目類型 ：BK FT
 * @param $overdue 足球封盤事件
 * @param $score_home 主隊比分
 * @param $score_away 客隊比分
 * @param $mid 賽事ID
 */
function send($type,$overdue=0,$score_home=0,$score_away=0,$mid,$log){
    $curl =new Curl_HTTP_Client();
    $send=array();
    $send['type']=$type;
    $send['overdue']=$overdue;
    $send['score_home']=$score_home;
    $send['score_away']=$score_away;
    $send['mid']=$mid;
    $curl->set_user_agent("Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36");
    $urls=array(
        'key1' =>"http://61.14.176.150/RBport.php",
	    'key2' =>"http://rb.lebole2.com/RBport.php",
		'key3' =>"http://hk.rb.lebole5.com/RBport.php",
		'key4' =>"http://rb.lebole5.com",
	   //'key3' =>"http://rb.lebole5.com/RBport.php"
    );
    foreach ($urls as $urlkey => $url) {
         $html=$curl->send_post_data($url,$send, "", 1);
        // $myfile = fopen("testfile.txt", "a");
        // fwrite($myfile, $html);
        // fwrite($myfile, "\r\n");
        // fwrite($myfile, $url);
        // fwrite($myfile, "\r\n");
        // fwrite($myfile, $send);
        // fwrite($myfile, "\r\n");
        // fwrite($myfile, $log);
        // fwrite($myfile, "\r\n");
        // fclose($myfile);
    }

}
?>
