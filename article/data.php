<?php 
    
    header('Content-Type: application/json');
    
    // Create connection to server
    require_once $_SERVER['DOCUMENT_ROOT'] .'/source/config.php';
    
    // Get the current article type
    // 1 = published
    // 2 = draft
    $type = $_POST['currentType'];
    
    class C_result 
    {
        public $error_code = 0;
        public $error_msg = "SUCCESS";
        public $data;
    }
    
    class C_data 
    {
        
        public $dataList = Array();
        public $page;
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
        public $hasNextPage = true;
    }
    
    class C_listcontent
    {
        public $createdAt = "2019/05/30";
        public $author = "Admin";
        public $id = 832;
        public $title = "Market Forecast for May 30th, 2019";
        public $info = "An article introduction!";
    }
    
    $result = new C_result;
    $result->data = new C_data;
    $result->data->page = new C_page;
    
    //For testing purposes:
    //$listcontent1 = new C_listcontent;
    //array_push($result->data->dataList, $listcontent1);
    
    // Each page displays 30 articles
    
    // The below code makes so that it displays only 30 items per page
    $current_page_number = $_POST["currentPage"];
    $result->data->page->currentPage = $current_page_number;
    $Offset = ($current_page_number - 1) * 30;
    
    // Display the correct article based on selected type (published or draft)
    if ($type == 1)
    {
        $sql = "SELECT date(created_at), author, id, name, intro FROM article WHERE type = 1 ORDER BY id DESC LIMIT 30 OFFSET $Offset";
    }
    else
    {
        $sql = "SELECT date(created_at), author, id, name, intro FROM article WHERE type = 2 ORDER BY id DESC LIMIT 30 OFFSET $Offset";
    }
    
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
        	    $listcontent->createdAt= $row[0];
        		$listcontent->author = $row[1];
                $listcontent->id= $row[2]; 
                $listcontent->title= $row[3]; 
                $listcontent->info= $row[4];
                
                array_push($result->data->dataList, $listcontent);
        	}
    	}
    }
    
    // Calculates the page numbers, total numbers, etc.
    if ($type == 1)
    {
        $sql_page = "SELECT COUNT(id) FROM article WHERE type = 1";
    }
    else
    {
        $sql_page = "SELECT COUNT(id) FROM article WHERE type != 1";
    }
    
    
    if ($sqlresult = $mysqli->query($sql_page))
    {
        if ($sqlresult->num_rows>0) 
    	{
    	    $row = $sqlresult->fetch_row();
    	    
    	    $totalNumber = $row[0];
    	    
    	    $result->data->page->totalNum = $totalNumber;
    	    $result->data->page->totalPage = ceil($totalNumber / 30);
    	    $result->data->page->totalPageNum = ceil($totalNumber / 30);
    	    
    	    //error_log("Total Number:".$totalNumber . "\n", 3, "data.log");
    	    //error_log("Total Pages:".ceil($totalNumber / 30) . "\n", 3, "data.log");
    	}
    }
    
    // Echo the results for display
    $jsonresult=json_encode($result, JSON_UNESCAPED_UNICODE);
    echo urldecode($jsonresult);
    $mysqli->close();
?>
