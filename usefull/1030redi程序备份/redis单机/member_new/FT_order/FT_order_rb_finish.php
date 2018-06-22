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
require("../include/define_function_list.inc.php");//赔率盘口转换
require("../include/curl_http.php");//http通信类
$uid = $_REQUEST['uid'];//会员唯一ID，登陆生成
$langx = $_REQUEST['langx'];//投注界面显示的语言(zh-cn 中文zh-tw 繁体 en-us 英文)
$gid = (int)$_REQUEST['gid'];//赛事ID在皇冠和平台唯1
$type = $_REQUEST['type'];//客队/主队让球赔率标识（C代表客队、H代表主队）
$rtype = $_REQUEST['rtype'];//当前所属玩法页面标签()
$gnum = $_REQUEST['gnum'];
$strong = $_REQUEST['strong'];
$bet_url = str_replace("&amp&&#40;59;", "&", $_REQUEST['bet_url']);

$odd_f_type = 'H';
$ioradio_r_h = $_REQUEST['ioradio_r_h'];
$gold = (int)$_REQUEST['gold'];
$active = $_REQUEST['active'];
$line = $_REQUEST['line_type'];
$restcredit = $_REQUEST['restcredit'];
require("../include/traditional.$langx.inc.php");// 语系标题变量文件表


if(1){
    $open = $memrow['OpenType'];
    $pay_type = $memrow['Pay_Type'];
    $memname = $memrow['UserName'];
    $agents = $memrow['Agents'];
    $world = $memrow['World'];
    $corprator = $memrow['Corprator'];
    $super = $memrow['Super'];
    $admin = $memrow['Admin'];
    $w_ratio = $memrow['ratio'];
    $HMoney = $memrow['Money'];
    if ($HMoney < $gold || $gold <= 0) {
        echo "<script>window.open('" . BROWSER_IP . "/tpl/logout_warn.html','_top')</script>";
        exit;
    }
    if ($gold < 100 && $line == 72) {
        echo "<script>window.open('" . BROWSER_IP . "/tpl/logout_warn.html','_top')</script>";
        exit;
    }
    if ($gold < 10) {
        echo "<script>window.open('" . BROWSER_IP . "/tpl/logout_warn.html','_top')</script>";
        exit;
    }
    $w_current = $memrow['CurType'];
    $havemoney = $HMoney - $gold;
    $memid = $memrow['ID'];

    $low_odds = $memrow['low_odds'];

    $mysql = "select datasite,uid from web_system_data where id=1";
    $result = mysql_db_query($dbname, $mysql);
    $row = mysql_fetch_array($result);
    $site = $row['datasite'];
    $suid = $row['uid'];
    include_once("../include/mul_ip.php");
    $curl = &new Curl_HTTP_Client(true);
    $curl->store_cookies("/var/www/html/manage/app/agents/downdata_ra/uid/cookies.txt");
    $curl->set_user_agent("Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36");


    $curl = &new Curl_HTTP_Client();
    $curl->store_cookies("cookies.txt");
    $curl->set_user_agent("Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36");
    switch ($line) {
        case '10'://全场大小
            $curl->set_referrer("" . $site . "/app/member/FT_index.php?rtype=re&uid=$suid&langx=zh-cn&mtype=3");
            $html_data = $curl->fetch_url("" . $site . "/app/member/FT_order/FT_order_rou.php?gid=$gid&uid=$suid&type=$type&gnum=$gnum&odd_f_type=$odd_f_type");

            break;
        case '9'://全场让球
            $curl->set_referrer("" . $site . "/app/member/FT_index.php?rtype=re&uid=$suid&langx=zh-cn&mtype=3");
            $html_data = $curl->fetch_url("" . $site . "/app/member/FT_order/FT_order_re.php?gid=$gid&uid=$suid&type=$type&gnum=$gnum&strong=$strong&odd_f_type=$odd_f_type");
            break;
        case '21'://全场独赢
            $curl->set_referrer("" . $site . "/app/member/FT_index.php?rtype=re&uid=$suid&langx=zh-cn&mtype=3");
            $html_data = $curl->fetch_url("" . $site . "/app/member/FT_order/FT_order_rm.php?gid=$gid&uid=$suid&type=$type&gnum=$gnum&odd_f_type=$odd_f_type");
            break;
        case '62'://总入球
            $ioradio_r_h = $_REQUEST['ioradio_pd'];
            $curl->set_referrer("" . $site . "/app/member/select.php?uid=$suid&langx=zh-cn&mtype=3");
            $html_data = $curl->fetch_url("" . $site . "/app/member/FT_order/FT_order_rt.php?gid=$gid&uid=$suid&rtype=$rtype&odd_f_type=$odd_f_type&langx=zh-cn");
            break;
        case '142'://半场波胆
            $ioradio_r_h = $_REQUEST['ioradio_pd'];
            $curl->set_referrer("" . $site . "/app/member/select.php?uid=$suid&langx=zh-cn&mtype=3");
            $gid = $gid + 1;
            $html_data = $curl->fetch_url("" . $site . "/app/member/FT_order/FT_order_hrpd.php?gid=$gid&uid=$suid&rtype=$rtype&odd_f_type=$odd_f_type&langx=zh-cn");
            $gid = $gid - 1;
            break;
        case '42'://全场波胆
            $ioradio_r_h = $_REQUEST['ioradio_pd'];
            $curl->set_referrer("" . $site . "/app/member/select.php?uid=$suid&langx=zh-cn&mtype=3");
            $html_data = $curl->fetch_url("" . $site . "/app/member/FT_order/FT_order_rpd.php?gid=$gid&uid=$suid&rtype=$rtype&odd_f_type=$odd_f_type&langx=zh-cn");
            break;
        case '72'://半场、全场
            $ioradio_r_h = $_REQUEST['ioradio_f'];
            $curl->set_referrer("" . $site . "/app/member/select.php?uid=$suid&langx=zh-cn&mtype=3");
            $html_data = $curl->fetch_url("" . $site . "/app/member/FT_order/FT_order_rf.php?gid=$gid&uid=$suid&rtype=$rtype&odd_f_type=$odd_f_type&langx=zh-cn");
            break;
        case '52'://单双
            $ioradio_r_h = $_REQUEST['ioradio_pd'];
            $curl->set_referrer("" . $site . "/app/member/select.php?uid=$suid&langx=zh-cn&mtype=3");
            $html_data = $curl->fetch_url("" . $site . "/app/member/FT_order/FT_order_rt.php?gid=$gid&uid=$suid&rtype=$rtype&odd_f_type=$odd_f_type&langx=zh-cn");
            break;
    }


    if ($line != 21) {
        preg_match('/<input.* name="gold"/Usi', $html_data, $m_temp);
    } else {
        preg_match('/<input.* name="gold"/Usi', $html_data, $m_temp);
    }

    if (!$m_temp) {
        echo "<script>window.location.href = '/app/member/wager_finish.php?uid=$uid&langx=$langx&10=10&uid=$suid&&web=$url_mul';</script>";
        exit();
    }
    //比对正网赔率
    /*preg_match('/<span  id=\"ioradio_id\" class=\"redFatWord\">([0-9\.]{1,8})<\/span>/Usi', $html_data, $rates);
    if($ioradio_r_h!=$rates[1]){
        echo "<script>window.location.href = '/app/member/wager_finish.php?uid=$uid&langx=$langx&1=20';</script>";
        exit;
    }*/
//全场波胆[42]、总入球[62]、 半场|全场[72] 半场波胆[142]s
    if ($line == 42 || $line == 62 || $line == 72 || $line == 142) {//投注那个玩法就更新到数据源表中那个字段
        preg_match('/<span  id=\"ioradio_id\" class=\"redFatWord\">([0-9\.]{1,8})<\/span>/Usi', $html_data, $rates);
        if(empty($rates)){
            preg_match('/<strong class="light" id="ioradio_id">([0-9\.]{1,8})<\/strong>/Usi',$html_data,$rates);
        }        
        if (empty($rates) || !is_numeric($rates[1]) || (float)($rates[1]) < 0.02) {
            echo "<script>window.location.href = '/app/member/wager_finish.php?uid=$uid&langx=$langx&1=2';</script>";
            exit;
        } else {
            $uprate = 'ior_' . $rtype;
            if ($line == 142) {
                $uprate = $uprate . 'H';
            }
            if ($line == 42 && $rtype == "ROVH") {
                $uprate = 'ior_ROVC';
            }
            if ($line == 62) {
                $chg = array('R0~1' => 'ior_RT01', 'R2~3' => 'ior_RT23', 'R4~6' => 'ior_RT46', 'ROVER' => 'ior_ROVER');
                $uprate = $chg[$rtype];

                if (!empty($uprate)) {
                    mysql_query("update match_sports set {$uprate}={$rates[1]} where MID='$gid'") or die('err');;
                } else {
                    echo "<script>window.location.href = '/app/member/wager_finish.php?uid=$uid&langx=$langx&1=3';</script>";
                    exit;
                }
            } else {
                mysql_query("update match_sports set {$uprate}={$rates[1]} where MID='$gid'") or die('err');;
            }
        }
    }
//RB_show 标签为滚球 open 标识前台显示或不显示 MB_Team_tw不能为空，因为后台取水程序主要是从繁体服务器取水 如果MB_Team_tw为空表示未有取到水
    $mysql = "select * from `match_sports` where Type='FT' and `MID`='$gid' and Open=1 and RB_show=1 and source_type<>'BET' and MB_Team!='' and MB_Team_tw!=''";// and MB_Team_en!=''
    $result = mysql_db_query($dbname, $mysql);
    $row = mysql_fetch_array($result);
    $cou = mysql_num_rows($result);
	
	//redis缓存overdue赛事封盘
    $redis->select(1);
	$redisOverdue = json_decode($redis->get($gid),true);
	if ($cou == 0 || $redisOverdue['overdue'] == '0') {
        if ($memname == 'cc6669') {
            die('22');
        }
        echo "<script>window.location.href = '/app/member/wager_finish.php?uid=$uid&langx=$langx&1=4';</script>";
        exit();
    }
    /*  $w_tg_team=$row['TG_Team'];
        $w_tg_team_tw=$row['TG_Team_tw'];
        $w_tg_team_en=$row['TG_Team_en'];
        //主队名称
        $w_mb_team=$row['MB_Team'];//中文
        $w_mb_team_tw=$row['MB_Team_tw'];//繁体
        $w_mb_team_en=$row['MB_Team_en'];//英文 */
    //主队名称
    $w_mb_team = filiter_team(trim($row['MB_Team']));//中文
    $w_mb_team_tw = filiter_team(trim($row['MB_Team_tw']));//繁体
    $w_mb_team_en = filiter_team(trim($row['MB_Team_en']));//英文
    //客队伍名称
    $w_tg_team = filiter_team(trim($row['TG_Team']));
    $w_tg_team_tw = filiter_team(trim($row['TG_Team_tw']));
    $w_tg_team_en = filiter_team(trim($row['TG_Team_en']));

    //取出当前赛事比赛到第XX分钟
    $now_play = $row['now_play'];
    if ($now_play) {
        $type1 = explode('||', $now_play);
        $type2 = $type1[1].'^'.$type1[0];
    }
    $s_mb_team = filiter_team($row[$mb_team]);
    $s_tg_team = filiter_team($row[$tg_team]);


    $m_date = $row["M_Date"];//赛事开始时间(yyyy-mm-dd)
    $showtype = $row["ShowTypeRB"];//滚球.客队/主队让球赔率标识（C代表客队、H代表主队）
    $bettime = date('Y-m-d H:i:s');//投注时间
    $m_start = strtotime($row['M_Start']);//赛事开始时间(yyyy-mm-dd hh:mm:ss)
    $datetime = time();
    if ($datetime - $m_start < 120) {//赛事开始前二分钟不能投注
        if ($memname == 'cc6669') {
            die('33');
        }
        echo "<script>window.location.href = '/app/member/wager_finish.php?uid=$uid&langx=$langx&1=5';</script>";
        exit();
    }
    //联盟
    if ($row[$m_sleague] == '') {
        $w_sleague = $row['M_League'];//联赛名称中文
        $w_sleague_tw = $row['M_League_tw'];// 繁体
        $w_sleague_en = $row['M_League_en'];//英文
        $s_sleague = $row[$m_league];
    }

    $inball = $row['MB_Ball'] . ":" . $row['TG_Ball'];//比分
    $inball1 = $inball;
    $mb_ball = $row['MB_Ball'];//主队进球
    $tg_ball = $row['TG_Ball'];//客队进球
    switch ($line) {
        case 21: //独赢
            $bet_type = '滚球独赢';
            $bet_type_new = '(滚球)&nbsp;独赢';
            $bet_type_tw = '滾球獨贏';
            $bet_type_en = "Running 1x2";
            $caption = $Order_FT . $Order_Running_1_x_2_betting_order;// Order_FT足球.单式独赢交易单
            $turn_rate = "FT_Turn_M";
            $turn = "FT_Turn_M";
            $MB_Rate = num_rate($open, $row["MB_Win_Rate_RB"]);
            $TG_Rate = num_rate($open, $row["TG_Win_Rate_RB"]);
            switch ($type) {
                case "H"://代表主队
                    $w_m_place = $w_mb_team;//主队名称
                    $w_m_place_tw = $w_mb_team_tw;
                    $w_m_place_en = $w_mb_team_en;
                    $s_m_place = $s_mb_team;
                    $w_m_rate = num_rate($open, $row["MB_Win_Rate_RB"]);
                    $turn_url = "/app/member/FT_order/FT_order_rm.php?gid=" . $gid . "&uid=" . $uid . "&type=" . $type . "&gnum=" . $gnum . "&odd_f_type=" . $odd_f_type;
                    $w_gtype = 'RMH';
                    break;
                case "C"://代表客队
                    $w_m_place = $w_tg_team;//客队名称
                    $w_m_place_tw = $w_tg_team_tw;
                    $w_m_place_en = $w_tg_team_en;
                    $s_m_place = $s_tg_team;
                    $w_m_rate = num_rate($open, $row["TG_Win_Rate_RB"]);
                    $turn_url = "/app/member/FT_order/FT_order_rm.php?gid=" . $gid . "&uid=" . $uid . "&type=" . $type . "&gnum=" . $gnum . "&odd_f_type=" . $odd_f_type;
                    $w_gtype = 'RMC';
                    break;
                case "N"://平局
                    $w_m_place = "和局";
                    $w_m_place_tw = "和局";
                    $w_m_place_en = "Flat";
                    $s_m_place = $Draw;
                    $w_m_rate = num_rate($open, $row["M_Flat_Rate_RB"]);
                    $turn_url = "/app/member/FT_order/FT_order_rm.php?gid=" . $gid . "&uid=" . $uid . "&type=" . $type . "&gnum=" . $gnum . "&odd_f_type=" . $odd_f_type;
                    $w_gtype = 'RMN';
                    break;
            }
            $Sign = "VS.";
            $grape = $type;
            $gwin = ($w_m_rate - 1) * $gold;
            $ptype = 'RM';
            break;
        case 9:
            $bet_type = '滚球让球';
            $bet_type_new = '(滚球)&nbsp;让球';
            $bet_type_tw = "滾球讓球";
            $bet_type_en = "Running Ball";
            $caption = $Order_FT . $Order_Running_Ball_betting_order;
            $turn_rate = "FT_Turn_RE_" . $open;
            $MB_LetB_Rate_RB = change_rate($open, $row["MB_LetB_Rate_RB"]);
            $TG_LetB_Rate_RB = change_rate($open, $row["TG_LetB_Rate_RB"]);
            $rate = get_other_ioratio($odd_f_type, $MB_LetB_Rate_RB, $TG_LetB_Rate_RB, 100);
            $MB_Rate = number_format($rate[0], 2);
            $TG_Rate = number_format($rate[1], 2);
            // if ($row['ShowTypeR'] == 'C') {
                // $Team = $MB_Rate;
                // $MB_Rate = $TG_Rate;
                // $TG_Rate = $Team;
            // }
            switch ($type) {
                case "H":
                    $w_m_place = $w_mb_team;
                    $w_m_place_tw = $w_mb_team_tw;
                    $w_m_place_en = $w_mb_team_en;
                    $s_m_place = $s_mb_team;
                    $w_m_rate = number_format($rate[0], 2);//主队赔率
                    $turn_url = "/app/member/FT_order/FT_order_re.php?gid=" . $gid . "&uid=" . $uid . "&type=" . $type . "&gnum=" . $gnum . "&strong=" . $strong . "&odd_f_type=" . $odd_f_type;
                    $w_gtype = 'RRH';
                    break;
                case "C":
                    $w_m_place = $w_tg_team;
                    $w_m_place_tw = $w_tg_team_tw;
                    $w_m_place_en = $w_tg_team_en;
                    $s_m_place = $s_tg_team;
                    $w_m_rate = number_format($rate[1], 2);//客队赔率
                    $turn_url = "/app/member/FT_order/FT_order_re.php?gid=" . $gid . "&uid=" . $uid . "&type=" . $type . "&gnum=" . $gnum . "&strong=" . $strong . "&odd_f_type=" . $odd_f_type;
                    $w_gtype = 'RRC';
                    break;
            }
            $Sign = $row['M_LetB_RB'];
            $grape = $Sign;
            $s_mb_team = $w_mb_team;
            if (strtoupper($showtype) == "H") {
                $l_team = $s_mb_team;
                $r_team = $s_tg_team;
                $w_l_team = $w_mb_team;
                $w_l_team_tw = $w_mb_team_tw;
                $w_l_team_en = $w_mb_team_en;
                $w_r_team = $w_tg_team;
                $w_r_team_tw = $w_tg_team_tw;
                $w_r_team_en = $w_tg_team_en;
                $inball = $row['MB_Ball'] . ":" . $row['TG_Ball'];
            } else {
                $r_team = $s_mb_team;
                $l_team = $s_tg_team;
                $w_r_team = $w_mb_team;
                $w_r_team_tw = $w_mb_team_tw;
                $w_r_team_en = $w_mb_team_en;
                $w_l_team = $w_tg_team;
                $w_l_team_tw = $w_tg_team_tw;
                $w_l_team_en = $w_tg_team_en;
                $inball = $row['TG_Ball'] . ":" . $row['MB_Ball'];
                $Team = $MB_Rate;
                $MB_Rate = $TG_Rate;
                $TG_Rate = $Team;
            }
            $s_tg_team = $r_team;
            $w_mb_team = $w_l_team;
            $w_mb_team_tw = $w_l_team_tw;
            $w_mb_team_en = $w_l_team_en;
            $w_tg_team = $w_r_team;
            $w_tg_team_tw = $w_r_team_tw;
            $w_tg_team_en = $w_r_team_en;
            $turn = "FT_Turn_RE";

            // if($memname=="sim1234"){
                // echo $w_mb_team."-----".$MB_Rate;
                // echo "********";
                // echo $w_tg_team."-----".$TG_Rate;
            // }

            if ($odd_f_type == 'H') {
                $gwin = ($w_m_rate) * $gold;
            } else if ($odd_f_type == 'M' or $odd_f_type == 'I') {
                if ($w_m_rate < 0) {
                    $gwin = $gold;
                } else {
                    $gwin = ($w_m_rate) * $gold;
                }
            } else if ($odd_f_type == 'E') {
                $gwin = ($w_m_rate - 1) * $gold;
            }
            $ptype = 'RE';
            break;
        case 10:
            $bet_type = '滚球大小';
            $bet_type_new = '(滚球)&nbsp;大&nbsp;/&nbsp;小';
            $bet_type_tw = "滾球大小";
            $bet_type_en = "Running Over/Under";
            $caption = $Order_FT . $Order_Running_Ball_Over_Under_betting_order;
            $turn_rate = "FT_Turn_OU_" . $open;
            $MB_Dime_Rate_RB = change_rate($open, $row["MB_Dime_Rate_RB"]);
            $TG_Dime_Rate_RB = change_rate($open, $row["TG_Dime_Rate_RB"]);
            $rate = get_other_ioratio($odd_f_type, $MB_Dime_Rate_RB, $TG_Dime_Rate_RB, 100);
            $MB_Rate = number_format($rate[0], 2);
            $TG_Rate = number_format($rate[1], 2);
            switch ($type) {
                case "C":
                    $w_m_place = $row["MB_Dime_RB"];
                    $w_m_place = str_replace('O', '大&nbsp;', $w_m_place);
                    $w_m_place_tw = $row["MB_Dime_RB"];
                    $w_m_place_tw = str_replace('O', '大&nbsp;', $w_m_place_tw);
                    $w_m_place_en = $row["MB_Dime_RB"];
                    $w_m_place_en = str_replace('O', 'over&nbsp;', $w_m_place_en);
                    $m_place = $row["MB_Dime_RB"];
                    $s_m_place = $row["MB_Dime_RB"];
                    $s_m_place_str = str_replace('O', '', $s_m_place);
                    if ($langx == "zh-cn") {
                        $s_m_place = str_replace('O', '大&nbsp;', $s_m_place);
                    } else if ($langx == "zh-tw") {
                        $s_m_place = str_replace('O', '大&nbsp;', $s_m_place);
                    } else if ($langx == "en-us" or $langx == 'th-tis') {
                        $s_m_place = str_replace('O', 'over&nbsp;', $s_m_place);
                    }
                    $peiqiu = str_replace('O', '', $row["MB_Dime_RB"]);
                    $peiqius = explode('/', $peiqiu);
                    $min = $peiqius[0] + 0;
                    if (($mb_ball + $tg_ball) > $min) {

                        echo "<script>window.location.href = '/app/member/wager_finish.php?uid=$uid&langx=$langx&1=6';</script>";
                        exit;
                    }
                    $w_m_rate = number_format($rate[0], 2);
                    $turn_url = "/app/member/FT_order/FT_order_rou.php?gid=" . $gid . "&uid=" . $uid . "&type=" . $type . "&gnum=" . $gnum . "&odd_f_type=" . $odd_f_type;
                    $w_gtype = 'ROUH';
                    break;
                case "H":
                    $w_m_place = $row["TG_Dime_RB"];
                    $w_m_place = str_replace('U', '小&nbsp;', $w_m_place);
                    $w_m_place_tw = $row["TG_Dime_RB"];
                    $w_m_place_tw = str_replace('U', '小&nbsp;', $w_m_place_tw);
                    $w_m_place_en = $row["TG_Dime_RB"];
                    $w_m_place_en = str_replace('U', 'under&nbsp;', $w_m_place_en);
                    $m_place = $row["TG_Dime_RB"];
                    $s_m_place = $row["TG_Dime_RB"];
                    $s_m_place_str = str_replace('U', '', $s_m_place);
                    if ($langx == "zh-cn") {
                        $s_m_place = str_replace('U', '小&nbsp;', $s_m_place);
                    } else if ($langx == "zh-tw") {
                        $s_m_place = str_replace('U', '小&nbsp;', $s_m_place);
                    } else if ($langx == "en-us" or $langx == 'th-tis') {
                        $s_m_place = str_replace('U', 'under&nbsp;', $s_m_place);
                    }
                    $w_m_rate = number_format($rate[1], 2);
                    $turn_url = "/app/member/FT_order/FT_order_rou.php?gid=" . $gid . "&uid=" . $uid . "&type=" . $type . "&gnum=" . $gnum . "&odd_f_type=" . $odd_f_type;
                    $w_gtype = 'ROUC';
                    break;
            }
            //@11
            //匹配盘口
            //$html_data = iconv("UTF-8", "UTF-8", $html_data);<tt class="RedWord fatWord">2.5</tt>
            preg_match('/<tt class="RedWord fatWord">([0-9\.]{1,8}) \/ ([0-9\.]{1,8})<\/tt>/Usi', $html_data, $rates);
            // var_dump($rates);
            // preg_match("/<em>([\w\W]+?)<\/em>/Usi", $html_data, $rates);
            
            // $rates = preg_split('/[\s+?|\/]+/', strip_tags($rates[1]));
            $s_m_place_str = str_replace(' ', '', str_replace('/', '', $s_m_place_str));
            $rates[0] = $rates[1] . $rates[2];
            if(empty($rates[0])){
                preg_match('/<tt class="RedWord fatWord">([0-9\.]{1,8})<\/tt>/Usi', $html_data, $rates);
                // var_dump($rates);exit();
                $rates[0] = $rates[1];
            }
            if ($s_m_place_str!=$rates[0]) {
                echo "<script>window.location.href = '/app/member/wager_finish.php?uid=$uid&langx=$langx&1=61';</script>";
                exit();
            }
            $Sign = "VS.";
            $grape = $m_place;
            $turn = "FT_Turn_OU";
            if ($odd_f_type == 'H') {
                $gwin = ($w_m_rate) * $gold;
            } else if ($odd_f_type == 'M' or $odd_f_type == 'I') {
                if ($w_m_rate < 0) {
                    $gwin = $gold;
                } else {
                    $gwin = ($w_m_rate) * $gold;
                }
            } else if ($odd_f_type == 'E') {
                $gwin = ($w_m_rate - 1) * $gold;
            }
            $ptype = 'ROU';
            break;
        case 42:
            $bet_type = '滚球波胆';
            $bet_type_new = '(滚球)&nbsp;波胆';
            $bet_type_tw = "滚球波膽";
            $bet_type_en = "Running Correct Score";
            $caption = $Order_FT . $Order_Correct_Score_betting_order;
            $turn_rate = "FT_Turn_RPD";
            if ($rtype != 'ROVH') {
                $rtype = str_replace("RH", "ior_RH", $rtype);
                $w_m_rate = $row[$rtype];
            } else {
                $w_m_rate = $row['ior_ROVC'];
            }
            if ($rtype == "ROVH") {
                $s_m_place = $Order_Other_Score;
                $w_m_place = '其它比分';
                $w_m_place_tw = '其它比分';
                $w_m_place_en = 'Other Score';
                $Sign = "VS.";
                $w_gtype = 'OVH';
            } else {
                $M_Place = "";
                $M_Sign = $rtype;
                $M_Sign = str_replace("ior_RH", "", $M_Sign);
                $M_Sign = str_replace("C", ":", $M_Sign);
                $w_m_place_en = $w_m_place_tw = $w_m_place = $s_m_place = $Sign = $M_Sign . "";
                $w_gtype = str_replace("ior_RH", "MB", $rtype);
                $w_gtype = str_replace("C", "TG", $w_gtype);
            }
            $grape = $odd_f_type;
            $turn = "FT_Turn_RPD";
            $gwin = ($w_m_rate - 1) * $gold;
            $ptype = 'RPD';

            break;
        case 52:
            $bet_type = '滚球单双';
            $bet_type_new = '(滚球)&nbsp;单双';
            $bet_type_tw = "滚球單雙";
            $bet_type_en = "Running Odd/Even";
            $caption = $Order_FT . $Order_Odd_Even_betting_order;
            $turn_rate = "FT_Turn_REO_" . $open;
            $MB_Rate = num_rate($open, $row["S_Single_Rate"]);
            $TG_Rate = num_rate($open, $row["S_Double_Rate"]);
            switch ($rtype) {
                case "RODD":
                    $w_m_place = '单';
                    $w_m_place_tw = '單';
                    $w_m_place_en = 'odd';
                    $s_m_place = '(' . $Order_Odd . ')';
                    $w_m_rate = num_rate($open, $row["S_Single_Rate"]);
                    break;
                case "REVEN":
                    $w_m_place = '双';
                    $w_m_place_tw = '雙';
                    $w_m_place_en = 'even';
                    $s_m_place = '(' . $Order_Even . ')';
                    $w_m_rate = num_rate($open, $row["S_Double_Rate"]);
                    break;
            }
            $Sign = "VS.";
            $turn = "FT_Turn_REO";
            $gwin = ($w_m_rate - 1) * $gold;
            $ptype = 'REO';
            $mtype = $w_gtype;
            $ioradio_r_h = $w_m_rate;
            // $grape = $rtype;
            $w_gtype=substr($rtype, 1, strlen($rtype) - 1);
            break;
        case 62:
            $bet_type = '滚球总入球';
            $bet_type_new = '(滚球)&nbsp;总入球';
            $bet_type_tw = "滚球總入球";
            $bet_type_en = "Running Total";
            $caption = $Order_FT . $Order_Total_Goals_betting_order;
            $turn_rate = "FT_Turn_T";
            switch ($rtype) {
                case "R0~1":
                    $w_m_place = '0~1';
                    $w_m_place_tw = '0~1';
                    $w_m_place_en = '0~1';
                    $s_m_place = '(0~1)';
                    $w_m_rate = $row["ior_RT01"];
                    break;
                case "R2~3":
                    $w_m_place = '2~3';
                    $w_m_place_tw = '2~3';
                    $w_m_place_en = '2~3';
                    $s_m_place = '(2~3)';
                    $w_m_rate = $row["ior_RT23"];
                    break;
                case "R4~6":
                    $w_m_place = '4~6';
                    $w_m_place_tw = '4~6';
                    $w_m_place_en = '4~6';
                    $s_m_place = '(4~6)';
                    $w_m_rate = $row["ior_RT46"];
                    break;
                case "ROVER":
                    $w_m_place = '7up';
                    $w_m_place_tw = '7up';
                    $w_m_place_en = '7up';
                    $s_m_place = '(7up)';
                    $w_m_rate = $row["ior_ROVER"];
                    break;
            }
            $turn = "FT_Turn_RT";
            $Sign = "VS.";
            $grape = $odd_f_type;
            $gwin = ($w_m_rate - 1) * $gold;
            $ptype = 'RT';
            $w_gtype = substr($rtype, 1, strlen($rtype) - 1);
            break;
        case 72:
            $bet_type = '滚球半全场';
            $bet_type_new = '(滚球)&nbsp;半全场';
            $bet_type_tw = "滚球半全場";
            $bet_type_en = "Running Half/Full Time";
            $caption = $Order_FT . $Order_Half_Full_Time_betting_order;
            $turn_rate = "FT_Turn_RF";
            switch ($rtype) {
                case "RFHH":
                    $w_m_place = $w_mb_team . '&nbsp;/&nbsp;' . $w_mb_team;
                    $w_m_place_tw = $w_mb_team_tw . '&nbsp;/&nbsp;' . $w_mb_team_tw;
                    $w_m_place_en = $w_mb_team_en . '&nbsp;/&nbsp;' . $w_mb_team_en;
                    $s_m_place = $row[$mb_team] . '&nbsp;/&nbsp;' . $row[$mb_team];
                    $w_m_rate = $row["ior_RFHH"];
                    break;
                case "RFHN":
                    $w_m_place = $w_mb_team . '&nbsp;/&nbsp;和局';
                    $w_m_place_tw = $w_mb_team_tw . '&nbsp;/&nbsp;和局';
                    $w_m_place_en = $w_mb_team_en . '&nbsp;/&nbsp;Flat';
                    $s_m_place = $row[$mb_team] . '&nbsp;/&nbsp;' . $Draw;
                    $w_m_rate = $row["ior_RFHN"];
                    break;
                case "RFHC":
                    $w_m_place = $w_mb_team . '&nbsp;/&nbsp;' . $w_tg_team;
                    $w_m_place_tw = $w_mb_team_tw . '&nbsp;/&nbsp;' . $w_tg_team_tw;
                    $w_m_place_en = $w_mb_team_en . '&nbsp;/&nbsp;' . $w_tg_team_en;
                    $s_m_place = $row[$mb_team] . '&nbsp;/&nbsp;' . $row[$tg_team];
                    $w_m_rate = $row["ior_RFHC"];
                    break;
                case "RFNH":
                    $w_m_place = '和局&nbsp;/&nbsp;' . $w_mb_team;
                    $w_m_place_tw = '和局&nbsp;/&nbsp;' . $w_mb_team_tw;
                    $w_m_place_en = 'Flat&nbsp;/&nbsp;' . $w_mb_team_en;
                    $s_m_place = $Draw . '&nbsp;/&nbsp;' . $row[$mb_team];
                    $w_m_rate = $row["ior_RFNH"];
                    break;
                case "RFNN":
                    $w_m_place = '和局&nbsp;/&nbsp;和局';
                    $w_m_place_tw = '和局&nbsp;/&nbsp;和局';
                    $w_m_place_en = 'Flat&nbsp;/&nbsp;Flat';
                    $s_m_place = $Draw . '&nbsp;/&nbsp;' . $Draw;
                    $w_m_rate = $row["ior_RFNN"];
                    break;
                case "RFNC":
                    $w_m_place = '和局&nbsp;/&nbsp;' . $w_tg_team;
                    $w_m_place_tw = '和局&nbsp;/&nbsp;' . $w_tg_team_tw;
                    $w_m_place_en = 'Flat&nbsp;/&nbsp;' . $w_tg_team_en;
                    $s_m_place = $Draw . '&nbsp;/&nbsp;' . $row[$tg_team];
                    $w_m_rate = $row["ior_RFNC"];
                    break;
                case "RFCH":
                    $w_m_place = $w_tg_team . '&nbsp;/&nbsp;' . $w_mb_team;
                    $w_m_place_tw = $w_tg_team_tw . '&nbsp;/&nbsp;' . $w_mb_team_tw;
                    $w_m_place_en = $w_tg_team_en . '&nbsp;/&nbsp;' . $w_mb_team_en;
                    $s_m_place = $row[$tg_team] . '&nbsp;/&nbsp;' . $row[$mb_team];
                    $w_m_rate = $row["ior_RFCH"];
                    break;
                case "RFCN":
                    $w_m_place = $w_tg_team . '&nbsp;/&nbsp;和局';
                    $w_m_place_tw = $w_tg_team_tw . '&nbsp;/&nbsp;和局';
                    $w_m_place_en = $w_tg_team_en . '&nbsp;/&nbsp;Flat';
                    $s_m_place = $row[$tg_team] . '&nbsp;/&nbsp;' . $Draw;
                    $w_m_rate = $row["ior_RFCN"];
                    break;
                case "RFCC":
                    $w_m_place = $w_tg_team . '&nbsp;/&nbsp;' . $w_tg_team;
                    $w_m_place_tw = $w_tg_team_tw . '&nbsp;/&nbsp;' . $w_tg_team_tw;
                    $w_m_place_en = $w_tg_team_en . '&nbsp;/&nbsp;' . $w_tg_team_en;
                    $s_m_place = $row[$tg_team] . '&nbsp;/&nbsp;' . $row[$tg_team];
                    $w_m_rate = $row["ior_RFCC"];
                    break;
            }
            $Sign = "VS.";
            $turn = "FT_Turn_RF";
            $gwin = ($w_m_rate - 1) * $gold;
            $ptype = 'RF';
            $grape = $w_gtype = substr($rtype, 1, strlen($rtype) - 1);
            break;

        case 142:
            $bet_type = '滚球半场波胆';
            $bet_type_new = '(滚球)&nbsp;半场&nbsp;波胆';
            $bet_type_tw = "滚球半場波膽";
            $bet_type_en = "Running 1st Half Correct Score";
            $caption = $Order_FT . $Order_1st_Half_Correct_Score_betting_order;
            $btype = "-&nbsp;<font color=red><b>[$Order_1st_Half]</b></font>";
            $turn_rate = "FT_Turn_PD";
            if ($rtype != 'ROVH') {
                //$rtype=str_replace('C','TG',str_replace('H','MB',$rtype));
                $w_m_rate = $row['ior_' . $rtype . 'H'];
            } else {
                $w_m_rate = $row['ior_ROVCH'];
            }
            if ($rtype == "ROVH") {
                $s_m_place = $Order_Other_Score;
                $w_m_place = '其它比分';
                $w_m_place_tw = '其它比分';
                $w_m_place_en = 'Other Score';
                $Sign = "VS.";
                $w_gtype = 'OVH';
            } else {
                $M_Place = "";
                $M_Sign = $rtype;
                $M_Sign = str_replace("RH", "", $M_Sign);
                $M_Sign = str_replace("C", ":", $M_Sign);
                $w_m_place_en = $w_m_place_tw = $w_m_place = $s_m_place = $Sign = $M_Sign . "";
                $w_gtype = str_replace("RH", "MB", str_replace('C', 'TG', $rtype));
            }
            $grape = "";
            $turn = "FT_Turn_HRPD";
            $gwin = ($w_m_rate - 1) * $gold;
            $ptype = 'VPD';
            //$w_gtype=substr(substr($rtype,1,strlen($rtype)-1),0,-1);
            $grape = $rtype;
            break;
    }
    if ($line == 42 || $line == 142) {
        if ($rtype == "ROVH") {
            if ($mb_ball >= 5 || $tg_ball >= 5) {

                echo "<script>window.location.href = '/app/member/wager_finish.php?uid=$uid&langx=$langx&1=7';</script>";
                exit();
            }
        }
    }
    if ($gold < 10) {
        if ($memname == 'cc6669') {
            die('44');
        }
        echo "<script>window.location.href = '/app/member/wager_finish.php?uid=$uid&langx=$langx&1=8';</script>";
        exit;
    }

    if($line!=52){
        if ($w_m_rate == '' or $grape == '') {
            if ($memname == 'cc6669') {
                die('55');
            }
            echo "<script>window.location.href = '/app/member/wager_finish.php?uid=$uid&langx=$langx&1=9';</script>";
            exit;
        }
    }

    if ($w_m_rate * 1 <= 0.05) {
        if ($memname == 'cc6669') {
            die('66');
        }
        echo "<script>window.location.href = '/app/member/wager_finish.php?uid=$uid&langx=$langx&1=10';</script>";
        exit;
    }
    if ($w_m_rate != $ioradio_r_h) {

        $turn_url = $turn_url . '&error_flag=1&langx=' . $langx;
        echo "<script language='javascript'>self.location='$turn_url';</script>";
        exit;
    }
    //if($line<>42){
    if ($s_m_place == '' or $w_m_place == '' or $w_m_place_tw == '') {
        if ($memname == 'cc6669') {
            die('77');
        }
        echo "<script>window.location.href = '/app/member/wager_finish.php?uid=$uid&langx=$langx&1=11';</script>";
        exit;
        //}
    }
    if ($line == 9 or $line == 10) {
        $oddstype = $odd_f_type;
    } else {
        $oddstype = '';
    }
    $w_mb_mid = $row['MB_MID'];
    $w_tg_mid = $row['TG_MID'];

    $lines = $row['M_League'] . "<br>[" . $row['MB_MID'] . ']vs[' . $row['TG_MID'] . "]<br>" . $w_mb_team . "&nbsp;<FONT COLOR=#cc0000><b>[" . $MB_Rate . "]</b></FONT>" . "&nbsp;&nbsp;<FONT COLOR=#0000BB><b>" . $Sign . "</b></FONT>&nbsp;&nbsp;" . $w_tg_team . "&nbsp;<FONT COLOR=#cc0000><b>[" . $TG_Rate . "]</b></FONT>" . "&nbsp;&nbsp;<FONT color=red><b>$inball</b></FONT><br>";
    $lines = $lines . "<FONT color=#cc0000>$w_m_place</FONT>&nbsp;@&nbsp;<FONT color=#cc0000><b>" . $w_m_rate . "</b></FONT>";

    $lines_tw = $row['M_League_tw'] . "<br>[" . $row['MB_MID'] . ']vs[' . $row['TG_MID'] . "]<br>" . $w_mb_team_tw . "&nbsp;<FONT COLOR=#cc0000><b>[" . $MB_Rate . "]</b></FONT>" . "&nbsp;&nbsp;<FONT COLOR=#0000BB><b>" . $Sign . "</b></FONT>&nbsp;&nbsp;" . $w_tg_team_tw . "&nbsp;<FONT COLOR=#cc0000><b>[" . $TG_Rate . "]</b></FONT>" . "&nbsp;&nbsp;<FONT color=red><b>$inball</b></FONT><br>";
    $lines_tw = $lines_tw . "<FONT color=#cc0000>$w_m_place_tw</FONT>&nbsp;@&nbsp;<FONT color=#cc0000><b>" . $w_m_rate . "</b></FONT>";

    $lines_en = $row['M_League_en'] . "<br>[" . $row['MB_MID'] . ']vs[' . $row['TG_MID'] . "]<br>" . $w_mb_team_en . "&nbsp;<FONT COLOR=#cc0000><b>[" . $MB_Rate . "]</b></FONT>" . "&nbsp;&nbsp;<FONT COLOR=#0000BB><b>" . $Sign . "</b></FONT>&nbsp;&nbsp;" . $w_tg_team_en . "&nbsp;<FONT COLOR=#cc0000><b>[" . $TG_Rate . "]</b></FONT>" . "&nbsp;&nbsp;<FONT color=red><b>$inball</b></FONT><br>";
    $lines_en = $lines_en . "<FONT color=#cc0000>$w_m_place_en</FONT>&nbsp;@&nbsp;<FONT color=#cc0000><b>" . $w_m_rate . "</b></FONT>";

    $ip_addr = get_ip();

    $m_turn = 0;
    $a_rate = 0;
    $b_rate = 0;
    $c_rate = 0;
    $d_rate = 0;

    $a_point = 100;
    $b_point = 0;
    $c_point = 0;
    $d_point = 0;
//低赔率不通过
    $line_array = array(1, 5, 11, 21, 31);    //1,独赢;5,单双;11,半场独赢
    if (in_array($line, $line_array)) {
        if ($w_m_rate < 1 + $low_odds) {
            $low_odds_alert = number_format(1 + $low_odds, 2);
            $bet_url .= "&islow_odds=1&low_odds=" . $low_odds;
            echo "<script>";
            echo "window.location.href='$bet_url'";
            echo "</script>";
            exit();
        }
    } else {
        if ($w_m_rate < $low_odds) {
            $bet_url .= "&islow_odds=1&low_odds=" . $low_odds;
            echo "<script>";
            echo "window.location.href='$bet_url'";
            echo "</script>";
            exit();
        }
    }

//空白注单
    if ($m_date == "" or $m_date == "0000-00-00" or $gold == 0 or $gold == "") {
        echo "<script>window.location.href = '/app/member/wager_finish.php?uid=$uid&langx=$langx';</script>";
        exit();
    }

    $sql = "update web_member_data set Money=Money-'$gold' where UserName='$memname' and money>='$gold'";
    $result_money = mysql_db_query($dbname, $sql);
    if (mysql_errno()==0 && mysql_affected_rows()>0) {
//$sql = "INSERT INTO web_report_data   (QQ83068506,danger,MID,Active,LineType,Mtype,M_Date,BetTime,BetScore,Middle,Middle_tw,Middle_en,BetType,BetType_tw,BetType_en,M_Place,M_Rate,M_Name,Gwin,TurnRate,OpenType,OddsType,ShowType,Agents,World,Corprator,Super,Admin,A_Rate,B_Rate,C_Rate,D_Rate,A_Point,B_Point,C_Point,D_Point,BetIP,Ptype,Gtype,CurType,Ratio,MB_MID,TG_MID,Pay_Type,Orderby,MB_Ball,TG_Ball,LastBetMoney,type) values ('$inball1','0','$gid','$active','$line','$w_gtype','$m_date','$bettime','$gold','$lines','$lines_tw','','$bet_type','$bet_type_tw','$bet_type_en','$grape','$w_m_rate','$memname','$gwin','$m_turn','$open','$oddstype','$showtype','$agents','$world','$corprator','$super','$admin','$a_rate','$b_rate','$c_rate','$d_rate','$a_point','$b_point','$c_point','$d_point','$ip_addr','$ptype','FT','$w_current','$w_ratio','$w_mb_mid','$w_tg_mid','$pay_type','$order','$mb_ball','$tg_ball','$HMoney','$type2')";
        $sql = "INSERT INTO web_report_data (QQ83068506,danger,MID,Active,LineType,Mtype,M_Date,BetTime,BetScore,Middle,Middle_tw,Middle_en,BetType,BetType_tw,BetType_en,M_Place,M_Rate,M_Name,Gwin,TurnRate,OpenType,OddsType,ShowType,Agents,World,Corprator,Super,Admin,A_Rate,B_Rate,C_Rate,D_Rate,A_Point,B_Point,C_Point,D_Point,BetIP,Ptype,Gtype,CurType,Ratio,MB_MID,TG_MID,Pay_Type,Orderby,MB_Ball,TG_Ball,LastBetMoney,type) values ('$inball1','1','$gid','$active','$line','$w_gtype','$m_date','$bettime','$gold','$lines','$lines_tw','','$bet_type','$bet_type_tw','$bet_type_en','$grape','$w_m_rate','$memname','$gwin','$m_turn','$open','$oddstype','$showtype','$agents','$world','$corprator','$super','$admin','$a_rate','$b_rate','$c_rate','$d_rate','$a_point','$b_point','$c_point','$d_point','$ip_addr','$ptype','FT','$w_current','$w_ratio','$w_mb_mid','$w_tg_mid','$pay_type','$order','$mb_ball','$tg_ball','$HMoney','$type2')";
        mysql_db_query($dbname, $sql) or die ("操作失败!");
        $ouid = mysql_insert_id();
    } else {
        die ("操作失败!");
    }
    mysql_close();
    ?>
    <html>
    <head>
        <meta http-equiv='Content-Type' content="text/html; charset=utf-8">
        <script language=javascript>
            window.setTimeout('sendsubmit()', 500);
            function sendsubmit() {
            }
        </script>
        <title></title>
        <link rel="stylesheet" href="/style/member/mem_order_ft<?= $css ?>.css?v=20161215 " type="text/css">


    </head>
    <script language="JavaScript" src="/js/order_finish.js?v=20161215 "></script>
    <body id="OFIN" class="bodyset" onSelectStart="self.event.returnValue=false"
          oncontextmenu="self.event.returnValue=false;window.event.returnValue=false;">
    <div class="ord ord_new">
        <div class="title"><h1>足球</h1></div>
        <div class="main">
            <div class="fin_title">
                <p class="fin_acc">下注成功</p>
                <p class="fin_uid">注单号：<?= show_voucher($line, $ouid) ?></p>
                <p class="fin_uid">投注时间：<?//=$bettime
                    ?></p>
            </div>
            <div class="gametype"><?= $bet_type_new ?></div>
            <div class="leag"><?= $s_sleague ?></div>
            <div class="teamName">
                <span class="tName"><?= $s_mb_team ?>&nbsp;&nbsp;<span class="radio">
                <span class="radio"><font color="#0000BB"><b>VS.</b></font></span></span>&nbsp;<?= $s_tg_team ?></span>
            </div>
            <p class="fin_team"><em><?= $s_m_place ?></em>&nbsp;@&nbsp;<strong><?= $w_m_rate ?></strong></p>

            <div class="betdata ft">
            <p class="fin_amount">交易金额：<span class="fin_gold"><?= $gold ?></span></p>
                  <p class="mayWin">可赢金额：<font id="pc"><?=$gwin?></font></p>
                  <p class="fin_uid">注单号：<span><?=show_voucher($line,$ouid)?></span></p>
                  <!-- <p class="fin_acc">下注成功</p> -->
                  <p class="error bottom-error">危险球 - 待确认</p>
            </div>
        </div>
        <div class="betBox">
            <!-- <input type="button" name="PRINT" value="列印" onClick="window.print()" class="print">  -->
            <input type="button" name="FINISH" value="确认" onClick="parent.close_bet();" class="close">
        </div>
    </div>
    </body>
    </html>
    <script>
        parent.parent.header.reloadCredit('RMB <?=$havemoney?>');
    </script>
    <?
}
mysql_close();
?>
