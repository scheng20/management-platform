<?php
    
    // Allows json stuff to be transported around
    header('Content-Type: application/json');
    
    //Config and Connect to Server
    require_once $_SERVER['DOCUMENT_ROOT'] .'/source/config.php';

    class C_result
    {
        public $data;
        public $error_code = 0;
        public $error_msg = 'SUCCESS';
    }
    
    class C_data
    {
        public $total; // Total number of comments
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
        public $hasNextPage = false;
    }
    
     class C_listcontent
    {
        public $publishTime = "06-17";
        public $messageNum = 17;
        public $publishDate = "2019-06-17";
    }
    
    // Creates the class (and all its subclasses) that are going to be transported to the actual page
    $result = new C_result;
    $result->data = new C_data;
    $result->data->page = new C_page;
    
    //Pushing results into the array for testing purposes.
    //$listcontent1 = new C_listcontent;
    //array_push($result->data->list, $listcontent1);
    
    // Each page displays 30 days
    
    // The below code makes so that it displays only 30 items per page
    $current_page_number = $_GET["currentPage"];
    $result->data->page->currentPage = $current_page_number;
    $Offset = ($current_page_number - 1) * 30;
    
    $sql = "SELECT count(id), DATE_FORMAT(date(created_at),'%m-%d'), date(created_at) FROM `user_comment` WHERE pid = 0 GROUP BY date(created_at) ORDER BY created_at DESC LIMIT 30 OFFSET $Offset";
    
    if ($sqlresult = $mysqli->query($sql))
    {
        if ($sqlresult->num_rows>0) 
        {
            if ($result->data->page->totalPageNum > 29) 
            {
                $result->data->page->hasNextpage = true;
            }

            while ($row=$sqlresult->fetch_row())
        	  {
        	      $listcontent = new C_listcontent;        
        		    $listcontent->messageNum= $row[0];
        		    $listcontent->publishTime = $row[1];
                $listcontent->publishDate= $row[2]; 
                
                array_push($result->data->list, $listcontent);
        	}

        }
    }
    
    // Calculates the page numbers
    
    $sql_page = "SELECT * FROM user_comment GROUP BY date(created_at)";
    
    if ($sqlresult = $mysqli->query($sql_page))
    {
        if ($sqlresult->num_rows>0) 
    	{
    	    $totalNumber = $sqlresult->num_rows;
    	    $result->data->page->totalNum = $totalNumber;
    	    $result->data->page->totalPage = ceil($totalNumber / 30);
    	    $result->data->page->totalPageNum = ceil($totalNumber / 30);
    	}
    }
    
    // Calculate the total number of comments
    $sql_TotalNum = "SELECT count(created_at) FROM user_comment WHERE pid = 0";
    
    if ($sqlresult = $mysqli->query($sql_TotalNum))
    {
        if ($sqlresult->num_rows>0) 
    	{
    	    $row = $sqlresult->fetch_row();
    	    
    	    $totalNumber = $row[0];
    	    
    	    $result->data->total = $totalNumber;
    	}
    }
    
    
    $jsonresult=json_encode($result, JSON_UNESCAPED_UNICODE);
    echo urldecode($jsonresult);
    $mysqli->close();

?>
