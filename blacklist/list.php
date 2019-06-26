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
        public $name = "some word";
        public $id = 140;
        public $updatedAt = "2018-12-27";
        public $status = 1;
    }
    
    $result = new C_result;
    $result->data = new C_data;
    $result->data->page = new C_page;
    
    // Each page displays 30 words
    
    // The below code makes so that it displays only 30 items per page
    $current_page_number = $_GET["currentPage"];
    $result->data->page->currentPage = $current_page_number;
    $Offset = ($current_page_number - 1) * 30;
    
    $sql = "SELECT id, name, status, date(updated_at) FROM key_words LIMIT 30 OFFSET $Offset";
    
    if ($sqlresult = $mysqli->query($sql))
    {
        $result->data->page->totalNum = $sqlresult->num_rows;
        
        if ($sqlresult->num_rows>0) 
        {
            $result->data->page->currentPage = $_GET["currentPage"];
            
            if ($result->data->page->totalPageNum > 1) 
            {
                $result->data->page->hasNextpage = true;
            }

            while ($row=$sqlresult->fetch_row())
        	{
        	      $listcontent = new C_listcontent;        
        		  $listcontent->id= $row[0];
        		  $listcontent->name = $row[1];
                  $listcontent->status= $row[2]; 
                  $listcontent->updatedAt= $row[3]; 
                
                array_push($result->data->list, $listcontent);
        	}

        }
    }
    
    // Calculates the page numbers, total numbers, etc.
    
    $sql_page = "SELECT COUNT(id) FROM key_words";
    
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
