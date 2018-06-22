<?php
	//连接本地的 Redis 服务
    //$redis = new Redis();
    //$redis->connect('127.0.0.1', 6378);
	//$redis->connect('192.168.12.102', 6379);
    //echo "Connection to server sucessfully";
    //查看服务是否运行
    //echo "Server is running: " . $redis->ping();
	$redis = new RedisCluster(NULL, ['192.168.12.56:6380', '192.168.12.56:6381', '192.168.12.56:6382', '192.168.12.56:6383', '192.168.12.56:6384', '192.168.12.56:6385']);
?>