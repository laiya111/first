<?php
	require ("../include/redis.conf.php");
	$sql="select MID,now_play,M_League,MB_MID,TG_MID,MB_Team,TG_Team,ShowTypeRB,M_LetB_RB,MB_LetB_Rate_RB,TG_LetB_Rate_RB,TG_Dime_RB,TG_Dime_Rate_RB,MB_Dime_Rate_RB,MB_Ball,TG_Ball,ShowTypeHRB,M_LetB_RB_H,MB_LetB_Rate_RB_H,MB_Dime_RB,TG_LetB_Rate_RB_H,MB_Dime_RB_H,TG_Dime_RB_H,TG_Dime_Rate_RB_H,MB_Dime_Rate_RB_H,MB_Card,TG_Card,MB_Red,TG_Red,MB_Win_Rate_RB,TG_Win_Rate_RB,M_Flat_Rate_RB,MB_Win_Rate_RB_H,TG_Win_Rate_RB_H,M_Flat_Rate_RB_H,Eventid,Hot,Play,M_Date,M_Time,mpid,S_Single_Rate,S_Double_Rate,RT,RF,RPD_show,overdue from match_sports left join match_sports_video_tmp on(MID=vmid)  where RB_show=1 and source_type<>'BET' and mb_team!='' and mb_team_tw !='' and type='FT' {$w2014} order by crown_order,m_start,m_league";
	// echo $sql; exit();
	$res=mysql_query($sql,$master) or die("查询match_sports: " . mysql_error());
	$cou=mysql_num_rows($res);
	$page_size=60;
	$page_count=ceil($cou/$page_size);
	// mysql_query("insert into match_sports_rctime(createtime,mt_type,type,src_type,context) values (current_timestamp,'RE','FT','DB','条数：".$cou."')",$master) or die("查询match记录并记录日志: " . mysql_error());
	echo "parent.gameCount='$cou';";
	echo "parent.t_page=$page_count;\n";
	echo "parent.retime=20;\n";
	echo "parent.retime_flag='Y';\n";
	echo "parent.str_renew='$second_auto_update';\n";
	echo "parent.game_more=1;\n parent.str_more='直播投注';";
	/*
	echo "parent.GameHead=new Array('gid','timer','league','gnum_h','gnum_c','team_h','team_c','strong','ratio','ior_RH','ior_RC','ratio_o','ratio_u','ior_OUH','ior_OUC','no1','no2','no3','score_h','score_c','hgid','hstrong','hratio','ior_HRH','ior_HRC','hratio_o','hratio_u','ior_HOUH','ior_HOUC','redcard_h','redcard_c','lastestscore_h','lastestscore_c','ior_MH','ior_MC','ior_MN','ior_HMH','ior_HMC','ior_HMN','str_odd','str_even','ior_EOO','ior_EOE','eventid','hot','center_tv','play','datetime','retimeset','more');\n";
	*/
	echo "parent.GameHead=new Array('gid','timer','league','gnum_h','gnum_c','team_h','team_c','strong','ratio','ior_RH','ior_RC','ratio_o','ratio_u','ior_OUH','ior_OUC','no1','no2','no3','score_h','score_c','hgid','hstrong','hratio','ior_HRH','ior_HRC','hratio_o','hratio_u','ior_HOUH','ior_HOUC','redcard_h','redcard_c','lastestscore_h','lastestscore_c','ior_MH','ior_MC','ior_MN','ior_HMH','ior_HMC','ior_HMN','str_odd','str_even','ior_EOO','ior_EOE','eventid','hot','center_tv','play','datetime','retimeset','more','sort_team_h','i_timer','sort_dy','sort_adjtimer');\n";
	$K=0;
	echo "parent.GameFT.length=0;";
	//redis缓存overdue赛事封盘
    //$redis->select(1);
	while($arr=mysql_fetch_assoc($res)){
		$pos = strpos($arr['M_League'],"测试");
		if(!($pos===false)){
			$cou--;
			continue;
		}
		if(!empty($arr['mpid'])){
			$arr['Eventid']=$arr['mpid'];
			$arr['Play']='Y';
		}
		else{
			$arr['Eventid']='0';
			$arr['Play']='';
		}
		$more11=$arr['RT']+0+$arr['RF']+$arr['RPD_show'];

		if ($arr['MB_LetB_Rate_RB']!=''){	//让球
			$more11++;
		}
		if(empty($arr['MB_LetB_Rate_RB']) || $arr['MB_LetB_Rate_RB']=="0.00"){
        	$arr['MB_LetB_Rate_RB']="";
        }
        if(empty($arr['TG_LetB_Rate_RB']) || $arr['TG_LetB_Rate_RB']=="0.00"){
        	$arr['TG_LetB_Rate_RB']="";
        }
		if ($arr['MB_Dime_Rate_RB']!=''){	//大小
			$more11++;
		}
		if(empty($arr['MB_Dime_Rate_RB']) || $arr['MB_Dime_Rate_RB']=="0.00"){
        	$arr['MB_Dime_Rate_RB']="";
        }
        if(empty($arr['TG_Dime_Rate_RB']) || $arr['TG_Dime_Rate_RB']=="0.00"){
        	$arr['TG_Dime_Rate_RB']="";
        }		
		if ($arr['MB_LetB_Rate_RB_H']!=''){	//半场让球
			$more11++;
		}
		if(empty($arr['MB_LetB_Rate_RB_H']) || $arr['MB_LetB_Rate_RB_H']=="0.00"){
        	$arr['MB_LetB_Rate_RB_H']="";
        }
        if(empty($arr['TG_LetB_Rate_RB_H']) || $arr['TG_LetB_Rate_RB_H']=="0.00"){
        	$arr['TG_LetB_Rate_RB_H']="";
        }
		if ($arr['MB_Dime_Rate_RB_H']!=''){	//半场大小
			$more11++;
		}
		if(empty($arr['MB_Dime_Rate_RB_H']) || $arr['MB_Dime_Rate_RB_H']=="0.00"){
        	$arr['MB_Dime_Rate_RB_H']="";
        }
        if(empty($arr['TG_Dime_Rate_RB_H']) || $arr['TG_Dime_Rate_RB_H']=="0.00"){
        	$arr['TG_Dime_Rate_RB_H']="";
        }
		//独赢
		if ($arr['TG_Win_Rate_RB']!=''){
		    $more11++;
		}
		if(empty($arr['MB_Win_Rate_RB']) || $arr['MB_Win_Rate_RB']=="0.00"){
        	$arr['MB_Win_Rate_RB']="";
        }
        if(empty($arr['TG_Win_Rate_RB']) || $arr['TG_Win_Rate_RB']=="0.00"){
        	$arr['TG_Win_Rate_RB']="";
        }
        if(empty($arr['M_Flat_Rate_RB']) || $arr['M_Flat_Rate_RB']=="0.00"){
        	$arr['M_Flat_Rate_RB']="";
        }
		//半场独赢
		if ($arr['MB_Win_Rate_RB_H']!=''){
		    $more11++; 
		}
		if(empty($arr['MB_Win_Rate_RB_H']) || $arr['MB_Win_Rate_RB_H']=="0.00"){
        	$arr['MB_Win_Rate_RB_H']="";
        }
        if(empty($arr['TG_Win_Rate_RB_H']) || $arr['TG_Win_Rate_RB_H']=="0.00"){
        	$arr['TG_Win_Rate_RB_H']="";
        }
        if(empty($arr['M_Flat_Rate_RB_H']) || $arr['M_Flat_Rate_RB_H']=="0.00"){
        	$arr['M_Flat_Rate_RB_H']="";
        }
		
		$nll=explode('||',$arr['now_play']);
		//---------add 2016/8/27 
		$tmp_arr=explode('^',$nll[1]); //2H^43
		$tmp_bc=$tmp_arr[0]; //1H半场  2H全场 
		$i_timer=$tmp_arr[1]; 
		if($tmp_bc=="1H") //上半场
		{
		   $i_timer=(int)$i_timer;
		}
		else if($tmp_bc=="MTIME") //中场
		{
		   $i_timer=59;
		}
		else if($tmp_bc=="2H") //下半场
		{
			$i_timer=(int)$i_timer;
			$i_timer=$i_timer+60;//加中场15分钟
		}
		
		
		$sort_dy=0;
		$tmp_mb_win_rate_rb=$arr['MB_Win_Rate_RB']<0.001?'':number_format($arr['MB_Win_Rate_RB'],2);
		if((float)$tmp_mb_win_rate_rb>0){
			$sort_dy=(float)$tmp_mb_win_rate_rb;	
		}
		
		$sort_team_h=$arr['MB_Team'];
		if(strstr($sort_team_h,'[中]')){
			$sort_team_h=str_replace('[中]','',$sort_team_h);	
		}
		
		//----------------------
		$oddRation = $arr['S_Single_Rate']?number_format($arr['S_Single_Rate'],2):"";
		$evenRation = $arr['S_Double_Rate']?number_format($arr['S_Double_Rate'],2):"";
		if(empty($arr['S_Single_Rate']) || $arr['S_Single_Rate']=="0.00"){
        	$oddRation="";
        }
        if(empty($arr['S_Double_Rate']) || $arr['S_Double_Rate']=="0.00"){
        	$evenRation="";
        }
		$sflag = $oddRation?"单":"";
		$dflag = $evenRation?"双":"";
		$arr['M_Date'] = str_replace(date('Y').'-','',$arr['M_Date']);
                        /*
                     * echo "parent.GameFT[$K]=new Array('{$arr['MID']}','$nll[0]','{$arr['M_League']}','{$arr['MB_MID']}','{$arr['TG_MID']}','{$arr['MB_Team']}','{$arr['TG_Team']}','{$arr['ShowTypeRB']}','".($arr['M_LetB_RB'])."','".($arr['MB_LetB_Rate_RB']<0.001?'':change_rate($open,$arr['MB_LetB_Rate_RB']))."','".($arr['TG_LetB_Rate_RB']<0.001?'':change_rate($open,$arr['TG_LetB_Rate_RB']))."','".($arr['MB_Dime_RB'])."','".($arr['TG_Dime_RB'])."','".($arr['TG_Dime_Rate_RB']<0.001?'':change_rate($open,$arr['TG_Dime_Rate_RB']))."','".($arr['MB_Dime_Rate_RB']<0.001?'':change_rate($open,$arr['MB_Dime_Rate_RB']))."','','','','$arr[MB_Ball]','$arr[TG_Ball]','".($arr['MID']+1)."','$arr[ShowTypeHRB]','".($arr['M_LetB_RB_H'])."','".($arr['MB_LetB_Rate_RB_H']<0.001?'':change_rate($open,$arr['MB_LetB_Rate_RB_H']))."','".($arr['TG_LetB_Rate_RB_H']<0.001?'':change_rate($open,$arr['TG_LetB_Rate_RB_H']))."','".($arr['MB_Dime_RB_H'])."','".($arr['TG_Dime_RB_H'])."','".($arr['TG_Dime_Rate_RB_H']<0.001?'':change_rate($open,$arr['TG_Dime_Rate_RB_H']))."','".($arr['MB_Dime_Rate_RB_H']<0.001?'':change_rate($open,$arr['MB_Dime_Rate_RB_H']))."','$arr[MB_Card]','$arr[TG_Card]','$arr[MB_Red]','$arr[TG_Red]','".($arr['MB_Win_Rate_RB']<0.001?'':number_format($arr['MB_Win_Rate_RB'],2))."','".($arr['TG_Win_Rate_RB']<0.001?'':number_format($arr['TG_Win_Rate_RB'],2))."','".($arr['M_Flat_Rate_RB']<0.001?'':number_format($arr['M_Flat_Rate_RB'],2))."','".($arr['MB_Win_Rate_RB_H']<0.001?'':number_format($arr['MB_Win_Rate_RB_H'],2))."','".($arr['TG_Win_Rate_RB_H']<0.001?'':number_format($arr['TG_Win_Rate_RB_H'],2))."','".($arr['M_Flat_Rate_RB_H']<0.001?'':number_format($arr['M_Flat_Rate_RB_H'],2))."','','','','','{$arr['Eventid']}','$arr[Hot]','','$arr[Play]','".$arr['M_Date'].'<br>'.$arr['M_Time']."','$nll[1]','$more11');\n";
                     */
        //if ($arr['overdue'] == 1)
	$redisOverdue = json_decode($redis->get($arr['MID']),true);		
	if (!empty($redisOverdue['overdue']) && $redisOverdue['overdue'] == '0')
			echo "parent.GameFT[$K]=new Array('{$arr['MID']}','$nll[0]','{$arr['M_League']}','{$arr['MB_MID']}','{$arr['TG_MID']}','{$arr['MB_Team']}','{$arr['TG_Team']}','','','','','','','','','','','','$arr[MB_Ball]','$arr[TG_Ball]','" . ($arr['MID'] + 1) . "','','','','','','','','','$arr[MB_Card]','$arr[TG_Card]','$arr[MB_Red]','$arr[TG_Red]','','','','','','','','','','','{$arr['Eventid']}','$arr[Hot]','','$arr[Play]','" . $arr['M_Date'] . '<br>' . $arr['M_Time'] . "','$nll[1]','0','$sort_team_h','$i_timer','$sort_dy','$i_timer');\n";
        else
			echo "parent.GameFT[$K]=new Array('{$arr['MID']}','$nll[0]','{$arr['M_League']}','{$arr['MB_MID']}','{$arr['TG_MID']}','{$arr['MB_Team']}','{$arr['TG_Team']}','{$arr['ShowTypeRB']}','" . ($arr['M_LetB_RB']) . "','" . ($arr['MB_LetB_Rate_RB'] < 0.001 ? '' : change_rate($open, $arr['MB_LetB_Rate_RB'])) . "','" . ($arr['TG_LetB_Rate_RB'] < 0.001 ? '' : change_rate($open, $arr['TG_LetB_Rate_RB'])) . "','" . ($arr['MB_Dime_RB']) . "','" . ($arr['TG_Dime_RB']) . "','" . ($arr['TG_Dime_Rate_RB'] < 0.001 ? '' : change_rate($open, $arr['TG_Dime_Rate_RB'])) . "','" . ($arr['MB_Dime_Rate_RB'] < 0.001 ? '' : change_rate($open, $arr['MB_Dime_Rate_RB'])) . "','','','','$arr[MB_Ball]','$arr[TG_Ball]','" . ($arr['MID'] + 1) . "','$arr[ShowTypeHRB]','" . ($arr['M_LetB_RB_H']) . "','" . ($arr['MB_LetB_Rate_RB_H'] < 0.001 ? '' : change_rate($open, $arr['MB_LetB_Rate_RB_H'])) . "','" . ($arr['TG_LetB_Rate_RB_H'] < 0.001 ? '' : change_rate($open, $arr['TG_LetB_Rate_RB_H'])) . "','" . ($arr['MB_Dime_RB_H']) . "','" . ($arr['TG_Dime_RB_H']) . "','" . ($arr['TG_Dime_Rate_RB_H'] < 0.001 ? '' : change_rate($open, $arr['TG_Dime_Rate_RB_H'])) . "','" . ($arr['MB_Dime_Rate_RB_H'] < 0.001 ? '' : change_rate($open, $arr['MB_Dime_Rate_RB_H'])) . "','$arr[MB_Card]','$arr[TG_Card]','$arr[MB_Red]','$arr[TG_Red]','" . ($arr['MB_Win_Rate_RB'] < 0.001 ? '' : number_format($arr['MB_Win_Rate_RB'], 2)) . "','" . ($arr['TG_Win_Rate_RB'] < 0.001 ? '' : number_format($arr['TG_Win_Rate_RB'], 2)) . "','" . ($arr['M_Flat_Rate_RB'] < 0.001 ? '' : number_format($arr['M_Flat_Rate_RB'], 2)) . "','" . ($arr['MB_Win_Rate_RB_H'] < 0.001 ? '' : number_format($arr['MB_Win_Rate_RB_H'], 2)) . "','" . ($arr['TG_Win_Rate_RB_H'] < 0.001 ? '' : number_format($arr['TG_Win_Rate_RB_H'], 2)) . "','" . ($arr['M_Flat_Rate_RB_H'] < 0.001 ? '' : number_format($arr['M_Flat_Rate_RB_H'], 2)) . "','{$sflag}','{$dflag}','{$oddRation}','{$evenRation}','{$arr['Eventid']}','$arr[Hot]','','$arr[Play]','" . $arr['M_Date'] . '<br>' . $arr['M_Time'] . "','$nll[1]','$more11','$sort_team_h','$i_timer','$sort_dy','$i_timer');\n";
            // echo "parent.GameFT[$K]=new Array('{$arr['MID']}','$nll[0]','{$arr['M_League']}','{$arr['MB_MID']}','{$arr['TG_MID']}','{$arr['MB_Team']}','{$arr['TG_Team']}','{$arr['ShowTypeRB']}','".($arr['M_LetB_RB'])."','".($arr['MB_LetB_Rate_RB']<0.001?'':change_rate($open,$arr['MB_LetB_Rate_RB']))."','".($arr['TG_LetB_Rate_RB']<0.001?'':change_rate($open,$arr['TG_LetB_Rate_RB']))."','".($arr['MB_Dime_RB'])."','".($arr['TG_Dime_RB'])."','".($arr['TG_Dime_Rate_RB']<0.001?'':change_rate($open,$arr['TG_Dime_Rate_RB']))."','".($arr['MB_Dime_Rate_RB']<0.001?'':change_rate($open,$arr['MB_Dime_Rate_RB']))."','','','','$arr[MB_Ball]','$arr[TG_Ball]','".($arr['MID']+1)."','$arr[ShowTypeHRB]','".($arr['M_LetB_RB_H'])."','".($arr['MB_LetB_Rate_RB_H']<0.001?'':change_rate($open,$arr['MB_LetB_Rate_RB_H']))."','".($arr['TG_LetB_Rate_RB_H']<0.001?'':change_rate($open,$arr['TG_LetB_Rate_RB_H']))."','".($arr['MB_Dime_RB_H'])."','".($arr['TG_Dime_RB_H'])."','".($arr['TG_Dime_Rate_RB_H']<0.001?'':change_rate($open,$arr['TG_Dime_Rate_RB_H']))."','".($arr['MB_Dime_Rate_RB_H']<0.001?'':change_rate($open,$arr['MB_Dime_Rate_RB_H']))."','$arr[MB_Card]','$arr[TG_Card]','$arr[MB_Red]','$arr[TG_Red]','".($arr['MB_Win_Rate_RB']<0.001?'':number_format($arr['MB_Win_Rate_RB'],2))."','".($arr['TG_Win_Rate_RB']<0.001?'':number_format($arr['TG_Win_Rate_RB'],2))."','".($arr['M_Flat_Rate_RB']<0.001?'':number_format($arr['M_Flat_Rate_RB'],2))."','".($arr['MB_Win_Rate_RB_H']<0.001?'':number_format($arr['MB_Win_Rate_RB_H'],2))."','".($arr['TG_Win_Rate_RB_H']<0.001?'':number_format($arr['TG_Win_Rate_RB_H'],2))."','".($arr['M_Flat_Rate_RB_H']<0.001?'':number_format($arr['M_Flat_Rate_RB_H'],2))."','','','','','{$arr['Eventid']}','$arr[Hot]','','$arr[Play]','".$arr['M_Date'].'<br>'.$arr['M_Time']."','$nll[1]','$more11','$sort_team_h','$i_timer','$sort_dy');\n";
		$K++;
	}
	
	//echo "alert('1=:'+parent.GameFT[0][2]+':'+parent.GameFT[0][50]);";
	echo "try{";
	if($sort_type == "C"){ //C按联盟排序  T按时间排序
		echo "parent.GameFT.sort(firstBy(function(v1,v2){return v1[2].localeCompare(v2[2]);}).
		                         thenBy(function(v1,v2){return parseInt(v2[50])-parseInt(v1[50]);}).
								 thenBy(function(v1,v2){return v1[5].localeCompare(v2[5]);}));";
	}
	else{ //按时间排序
		echo "re_adjdata_forsort_t();";
		echo "parent.GameFT.sort(firstBy(function(v1,v2){return parseInt(v2[53])-parseInt(v1[53]);}).
								 thenBy(function(v1,v2){return v1[2].localeCompare(v2[2]);}).
								 thenBy(function(v1,v2){return parseInt(v2[51])-parseInt(v1[51]);}).
		                         thenBy(function(v1,v2){return v1[50].localeCompare(v2[50]);}).
								 thenBy(function(v1,v2){return parseFloat(v2[52])-parseFloat(v1[52]);})
								 );";					 	
	}
	echo "}catch(e){}\n";
	
	/*
	echo "parent.GameFT.sort(firstBy(function(x, y){return y[50]-x[50];});";
	*/
	echo "parent.gamount=".$cou.";\n";
?>