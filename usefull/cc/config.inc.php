<?php
if ( function_exists("date_default_timezone_set"))date_default_timezone_set ("Etc/GMT+4");
$dbhost                                = "localhost";                 // 数据库主机名
$dbuser                                = "root";                      // 数据库用户名
$dbpass                                = "ms7q5HuC96Q8nhzu";      // 数据库密码
$dbname                                = "crown8886686";                // 数据库名
mysql_connect($dbhost,$dbuser,$dbpass);

$str="select|update|from|where|order|delete|insert|values|create|database";  //非法字符 
$arr=explode("|",$str);//数组非法字符，变单个 
foreach ($_REQUEST as $key=>$value){
	for($i=0;$i<sizeof($arr);$i++){
		if (substr_count(strtolower($_REQUEST[$key]),$arr[$i])>0){       //检验传递数据是否包含非法字符 
		    echo "SQL通用防注入系统提示,请不要在参数中包含非法字符尝试注入!";  
            exit; //退出不再执行后面的代码
		}
	} 
}


$sql = "select * from web_marquee_data order by ID desc limit 0,1";
$result = mysql_db_query($dbname,$sql);
$row = mysql_fetch_array($result);
$msg_member=$row['Message'];
$msg_member_tw=$row['Message_tw'];
$msg_member_en=$row['Message_en'];
?>
