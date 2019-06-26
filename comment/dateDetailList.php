<?php

    header('Content-Type: application/json');
    
    // Create connection to server
    require_once $_SERVER['DOCUMENT_ROOT'] .'/source/config.php';
    
    class C_result
    {
        public $data;
        public $error_code = 0;
        public $error_msg = "SUCCESS";
    }
    
    class C_data
    {
        public $page;
        public $list = Array();
    }

    class C_page
    {
        public $pageSize = 30;
        public $currentPage = 1;
        public $totalNum = 0;
        public $startIndex = 0;
        public $endIndex = 30;
        public $totalPage = 1;
        public $totalPageNum = 1;
        public $hasNextPage = true;
    }
    
    class C_listcontent
    {
        public $id = 223428;
        public $content = "This is a main comment!";
        public $userId = 67;
        public $pid = 0;
        public $moduleId = 2;
        public $status = 1;
        public $type = 1;
        public $isRecommend = 1;
        public $likeNum = 1;
        public $updatedAt = "06/26/2019";
        public $createdAt = "06/26/2019";
        public $userName = "Some UserName";
        public $vipType = 15;
        public $headImgUrl = "IMG URL";
        public $publishTime = "22:04";
        public $isLike = 0;
        public $list = Array();
        public $newStrategyFlag = 0;
    }
    
    class C_replyContent
    {
        public $isRecommend = 1;
        public $replyTime = "06-27 22:32";
        public $id = 223429;
        public $userName = "Another UserName";
        public $content = "This is a reply to a main comment!";
    }
    
    $result = new C_result;
    $result->data = new C_data; 
    $result->data->page = new C_page;
    
    $listcontent = new C_listcontent;
    $replyContent = new C_replyContent;
    
    //For testing purposes:
    //array_push($listcontent->list, $replyContent);
    //array_push($result->data->list, $listcontent);
    
    // Each page displays 30 main comments
    
    // The below code makes so that it displays only 30 items per page
    $current_page_number = $_GET["currentPage"];
    $result->data->page->currentPage = $current_page_number;
    $Offset = ($current_page_number - 1) * 30;
    
    $publishTime = $_GET["publishTime"];
    
    $sql = "SELECT * FROM user_comment INNER JOIN (SELECT id, user_name, head_imgurl, vip_type FROM user) AS r ON r.id = user_comment.user_id WHERE user_comment.pid = 0 AND date(user_comment.created_at) = '$publishTime' ORDER BY user_comment.created_at DESC LIMIT 30 OFFSET $Offset";
    
    // Fill in the main comment thread
    if ($sqlresult = $mysqli->query($sql))
    {
        if ($sqlresult->num_rows>0) 
        {
            if ($result->data->page->totalPageNum > 29) 
            {
                $result->data->page->hasNextpage = true;
            }
            else
            {
                $result->data->page->hasNextpage = false;
            }

            while ($row=$sqlresult->fetch_row())
            {
        	    $listcontent = new C_listcontent;        
        		$listcontent->id= $row[0];
        		$listcontent->content = $row[1];
                $listcontent->user_id = $row[2]; 
                $listcontent->pid = $row[3]; 
                $listcontent->module_id = $row[4]; 
                $listcontent->status = $row[5];
                $listcontent->type = $row[6];
                $listcontent->isRecommend = $row[7];
                $listcontent->likeNum = $row[8];
                $listcontent->updatedAt = $row[9];
                $listcontent->createdAt = $row[10];
                $listcontent->userName = $row[12];
                $listcontent->headImgUrl = $row[13];
                $listcontent->vip_type = $row[14];
                $listcontent->publishTime = date("h:ia", strtotime($row[10]));
                
                $replyID = $row[0];
                
                // Find any replies to the main comment thread
                $sqlReply = "SELECT user_comment.id, user_comment.content, user_comment.user_id, user_comment.pid, user_comment.created_at, user_comment.is_recommend, r.user_name FROM user_comment INNER JOIN (SELECT id, user_name FROM user) as r on r.id = user_comment.user_id WHERE user_comment.pid = $replyID ORDER BY user_comment.created_at ASC";
                
                if ($sqlReplyResult = $mysqli->query($sqlReply))
                {
                    if ($sqlReplyResult->num_rows>0) 
                    {
                        while ($replyRow=$sqlReplyResult->fetch_row())
        	            {
                            $replyContent = new C_replyContent;
                            $replyContent->id = $replyRow[0];
                            $replyContent->content = $replyRow[1];
                            $replyContent->replyTime = $replyRow[4]; 
                            $replyContent->isRecommend = $replyRow[5];
                            $replyContent->userName = $replyRow[6];
                            
                            array_push($listcontent->list, $replyContent);
        	            }
                    }
                 }
                
                array_push($result->data->list, $listcontent);
            }

         }
    }
    
    // Calculates the page numbers, total numbers, etc.
    
    $sql_page = "SELECT count(id) FROM user_comment WHERE pid = 0 AND date(created_at) = '$publishTime' ORDER BY created_at DESC";
    
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
    
    $jsonresult=json_encode($result, JSON_UNESCAPED_UNICODE);
    echo urldecode($jsonresult);
    $mysqli->close();

?>
