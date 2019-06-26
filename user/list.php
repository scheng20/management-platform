<?php

    header('Content-Type: application/json');
    
    //Create connection to server
    require_once $_SERVER['DOCUMENT_ROOT'] .'/source/config.php';
    
    class C_result
    {
        public $data;
        public $error_code = 0;
        public $error_msg = 'SUCCESS';
    }
    
    class C_data
    {
        public $page;
        public $list = Array();
        public $userName = '';
    }
    
    class C_page
    {
        public $pageSize = 30;
        public $currentPage = 1;
        public $totalNum = 0;
        public $startIndex = 0;
        public $endIndex = 30;
        public $totalPage = 0;
        public $totalPageNum = 0;
        public $hasNextPage = false;
    }
    
    class C_listcontent
    {
        public $headImg = 'www.example.com';
        public $phone = '888-888-888';
        public $validDate = '';
        public $regDate = '2016/10/22';
        public $id = 1;
        public $userName = 'This is a username';
        public $type = '';
        public $status = 1;
    }
    
    // Initalize a new result
    $result = new C_result;
    $result->data = new C_data;
    $result->data->page = new C_page;
    
    // Each page displays 30 users at a time.
    
    // Gets current page number & allows user to flip through multiple pages
    $current_page_number = $_GET["currentPage"];
    $result->data->page->currentPage = $current_page_number;
    $Offset = ($current_page_number - 1) * 30;
    
    //--------------------------------------------------------------
    // Gets actual user content from user database, only 30 users at a time.
    
    $searched_username = $_GET["userName"];
    
    // IF NO NAME IS SEARCHED 
    if ($searched_username == "")
    {
        $sql = "SELECT user.id, user.user_name, user.phone, user.head_imgurl, user.status, DATE_FORMAT(date(user_vip.start_time), '%d/%m/%Y'), DATE_FORMAT(date(user_vip.end_time), '%d/%m/%Y'), r.type
            FROM user
	            INNER JOIN user_vip ON user_vip.id =user.id
                INNER JOIN (
select t1.user_id,type from user_vip_log as t1
inner join (SELECT user_id, max(id) as maxid FROM `user_vip_log` GROUP BY user_id) 
as t2
where t1.id=t2.maxid) AS r ON r.user_id = user.id
            LIMIT 30 OFFSET $Offset";
            
        // Displays user content
        if ($sqlresult = $mysqli->query($sql))
        {
            if ($sqlresult->num_rows>0) 
            {
		    if ($sqlresult->num_rows>=29)
        	    {
        	         $result->data->page->hasNextPage = true;
        	    }
        	    else
        	    {
        	         $result->data->page->hasNextPage = false;
        	    }
		    
		    while ($row=$sqlresult->fetch_row())
		    {
			    $listcontent = new C_listcontent;
			    $listcontent->id= $row[0];
			    $listcontent->userName = $row[1];
			    $listcontent->phone= $row[2]; 
			    $listcontent->headImg= $row[3]; 
			    $listcontent->status= $row[4];
			    $listcontent->validDate = $row[5].'-'.$row[6];
			    
			    if ($row[7] == 1)
			    {
				    $listcontent->type = "Free Trial";
			    }
			    else if ($row[7] == 2)
			    {
				    $listcontent->type = "Monthly Membership";
			    }
			    else if ($row[7] == 3)
			    {
				    $listcontent->type = "Yearly Membership";
			    }
			    else if ($row [7] == 4)
			    {
				    $listcontent->type = "";
			    }
			    array_push($result->data->list, $listcontent);
            	}
            }
        }
        
        // Calculates the page numbers, total numbers, etc.
        $sql_page = "SELECT COUNT(id) FROM user";
    
        if ($sqlresult = $mysqli->query($sql_page))
        {
            if ($sqlresult->num_rows>0) 
        	{
        	    $row = $sqlresult->fetch_row();
        	    
        	    $totalNumber = $row[0];
        	    
        	    $result->data->page->totalNum = $totalNumber;
        	    $result->data->page->totalPage = ceil($totalNumber / 30);
        	    $result->data->page->totalPageNum = ceil($totalNumber / 30);
        	   
        	}
        }
    }
    else //IF A NAME IS SEARCHED
    {
        $stmt = $mysqli->prepare("SELECT user.id, user.user_name, user.phone, user.head_imgurl, user.status, DATE_FORMAT(date(user_vip.start_time), '%d/%m/%Y'), DATE_FORMAT(date(user_vip.end_time), '%d/%m/%Y'), r.type
            FROM user
	            INNER JOIN user_vip ON user_vip.id =user.id
                INNER JOIN (
select t1.user_id,type from user_vip_log as t1
inner join (SELECT user_id, max(id) as maxid FROM `user_vip_log` GROUP BY user_id) 
as t2
where t1.id=t2.maxid) AS r ON r.user_id = user.id
WHERE user.user_name LIKE ?
            LIMIT 30 OFFSET $Offset");
        
        // Check whether the prepare() succeeded
        if ($stmt === false) 
        {
            error_log('mysqli statement prepare error:'.$mysqli->error);
        }
        else
        {
            // Bind parameters
            $searched_username = "%{$_GET['userName']}%";
            $stmt->bind_param("s", $searched_username);
            
            // Execute the statement
            $stmt->execute();
            
            // Collect the results
            $stmt->bind_result($id, $username, $phone, $headImg, $status, $validStart, $validEnd, $type);
            
            while ($stmt->fetch())
            {
            	    $listcontent = new C_listcontent;        
            	    $listcontent->id= $id;
            	    $listcontent->userName = $username;
                    $listcontent->phone= $phone; 
                    $listcontent->headImg= $headImg; 
                    $listcontent->status= $status;
                    $listcontent->validDate = $validStart.'-'.$validEnd;
                    
                    if ($type == 1)
                    {
                        $listcontent->type = "Free Trial";
                    }
                    else if ($type == 2)
                    {
                        $listcontent->type = "Monthly Membership";
                    }
                    else if ($type == 3)
                    {
                        $listcontent->type = "Yearly Membership";
                    }
                    else if ($type == 4)
                    {
                        $listcontent->type = "";
                    }
                    
                    array_push($result->data->list, $listcontent);
            }
            
            // close statement
            $stmt->close();
            
            // Find the new page numbers
            $stmtPage = $mysqli->prepare("SELECT COUNT(user.id)
            FROM user
	            INNER JOIN user_vip ON user_vip.id =user.id
                INNER JOIN (
select t1.user_id,type from user_vip_log as t1
inner join (SELECT user_id, max(id) as maxid FROM `user_vip_log` GROUP BY user_id) 
as t2
where t1.id=t2.maxid) AS r ON r.user_id = user.id
WHERE user.user_name LIKE ?");

            // Check whether the prepare() succeeded
            if ($stmtPage === false) 
            {
                error_log('mysqli statement prepare error:'.$mysqli->error);
            }
            else
            {
                // Bind parameters
                $searched_username = "%{$_GET['userName']}%";
                $stmtPage->bind_param("s", $searched_username);
            
                // Execute
                $stmtPage->execute();
            
                // Recieve the data
                $stmtPage->bind_result($count);
                
                while ($stmtPage->fetch())
                {
                    	$result->data->page->totalNum = $count;
        	        $result->data->page->totalPage = ceil($count / 30);
        	        $result->data->page->totalPageNum = ceil($count / 30);
                }
                
            }
        	    
            // close statement
            $stmtPage->close();
        }
    }
    
    $jsonresult=json_encode($result, JSON_UNESCAPED_UNICODE);
    echo urldecode($jsonresult);
    $mysqli->close();
?>
