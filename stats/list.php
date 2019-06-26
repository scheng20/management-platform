<?php

    header('Content-Type: application/json');
    
    // Create connection to server
    require_once $_SERVER['DOCUMENT_ROOT'] .'/source/config.php';

    class C_result
    {
        public $data;
        public $error_code = 0;
        public $error_msg = 'SUCCESS';
    }
    
    class C_data
    {
        public $date = NULL;
        public $userNum = 0;
        public $monthVipNum = 0;
        public $yearVipNum = 0;
        public $list = Array();
    }
    
     class C_listcontent
    {
        public $date = NULL;
        public $monthVipCount = 0;
        public $userCount = 0;
        public $yearVipCount = 0;
    }
    
    $result = new C_result;
    $result->data = new C_data;
    
    $listcontent1 = new C_listcontent;
    $listcontent2 = new C_listcontent;
    
    $listcontent2->date = "2019-20-20";
    $listcontent2->monthVipCount = 1;
    $listcontent2->userCount = 2;
    $listcontent2->yearVipCount = 3;
    
    $sql_totalUser = 'SELECT COUNT(user.id)
            FROM user
	            INNER JOIN user_vip ON user_vip.id =user.id
                INNER JOIN (
select t1.user_id,type from user_vip_log as t1
inner join (SELECT user_id, max(id) as maxid FROM `user_vip_log` GROUP BY user_id) 
as t2
where t1.id=t2.maxid) AS r ON r.user_id = user.id';
    
    $sql_totalMonthU = 'SELECT COUNT(user.id)
            FROM user
	            INNER JOIN user_vip ON user_vip.id =user.id
                INNER JOIN (
select t1.user_id,type from user_vip_log as t1
inner join (SELECT user_id, max(id) as maxid FROM `user_vip_log` GROUP BY user_id) 
as t2
where t1.id=t2.maxid) AS r ON r.user_id = user.id WHERE r.type = 2';

    $sql_totalYearU = 'SELECT COUNT(user.id)
            FROM user
	            INNER JOIN user_vip ON user_vip.id =user.id
                INNER JOIN (
select t1.user_id,type from user_vip_log as t1
inner join (SELECT user_id, max(id) as maxid FROM `user_vip_log` GROUP BY user_id) 
as t2
where t1.id=t2.maxid) AS r ON r.user_id = user.id WHERE r.type = 3';
    
    if ($sqlresult = $mysqli->query($sql_totalUser))
    {
        $row = $sqlresult->fetch_row();
        $result->data->userNum = $row[0];
    }
    
    if ($sqlresult = $mysqli->query($sql_totalMonthU))
    {
        $row = $sqlresult->fetch_row();
        $result->data->monthVipNum = $row[0];
    }
    
     if ($sqlresult = $mysqli->query($sql_totalYearU))
    {
        $row = $sqlresult->fetch_row();
        $result->data->yearVipNum = $row[0];
    }
    
    for ($x = 0; $x < 31; $x++)
    {
        $listcontent = new C_listcontent; 
        
        // Add the date
        $date = date('Y-m-d',strtotime("-$x days"));
        $listcontent->date = $date;
        
        // Count the new users for that date
        $sql_newUsers = "SELECT COUNT(id) as NewUsers, date(created_at) as MYdate FROM user WHERE date(created_at) = '$date'";
        
        if ($sqlresult = $mysqli->query($sql_newUsers))
        {
            $row = $sqlresult->fetch_row();
            $listcontent->userCount = $row[0];
        }
        
        //Count the new monthly users for that date
        $sql_newMonthly = "SELECT COUNT(r.user_id) FROM (SELECT t1.user_id, type, updated_at
FROM user_vip_log AS t1
INNER JOIN
(SELECT user_id, MAX(id) as maxid FROM `user_vip_log`

GROUP BY user_id) AS t2
WHERE t1.id = t2.maxid) as r
WHERE date(r.updated_at) = '$date' and r.type = 2";
        
        if ($sqlresult = $mysqli->query($sql_newMonthly))
        {
            $row = $sqlresult->fetch_row();
            $listcontent->monthVipCount = $row[0];
        }
        
        $sql_newYearly = "SELECT COUNT(r.user_id) FROM (SELECT t1.user_id, type, updated_at
FROM user_vip_log AS t1
INNER JOIN
(SELECT user_id, MAX(id) as maxid FROM `user_vip_log`

GROUP BY user_id) AS t2
WHERE t1.id = t2.maxid) as r
WHERE date(r.updated_at) = '$date' and r.type = 3";
        
        if ($sqlresult = $mysqli->query($sql_newYearly))
        {
            $row = $sqlresult->fetch_row();
            $listcontent->yearVipCount = $row[0];
        }
    
        //Testing purposes: 
        //error_log($sql_newUsers . "\n", 3, "list.log");
        
        array_push($result->data->list, $listcontent);
    }
    
    $jsonresult=json_encode($result, JSON_UNESCAPED_UNICODE);
    echo urldecode($jsonresult);
    $mysqli->close();
?>
