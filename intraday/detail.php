<?php 
    header('Content-Type: application/json');
    require_once $_SERVER['DOCUMENT_ROOT'] .'/source/config.php';

    class C_result 
    {
        public $data;
        public $error_code=0;
        public $error_msg='SUCCESS';
    }
    
    class C_data 
    {
        public $page;
        public $list = Array();
    }

    class C_page 
    {
        public $pageSize = 1;
	      public $currentPage = 0;
	      public $totalNum = 910;
	      public $startIndex = 0;
	      public $endIndex= 1;
	      public $totalPage=911;
	      public $totalPageNum = 910;
	      public $hasNextPage = true;
    }

    class C_list 
    {
        public $publishDate = '2020-00-00';
        public $list = Array();
    }
    
    class C_listcontent 
    {
        public $publishTime = '10:0';
        public $id = 38399;
        public $content = 'Insightful comment';
    }
    
    $result = new C_result;
    $result->data = new C_data;
    $result->data->page = new C_page;
    
    $list = new C_list;

    $sql = 'SELECT date(publish_date) as MYdate FROM intraday_review group BY date(publish_date) order by MYdate DESC';
    
    if ($sqlresult = $mysqli->query($sql))
    {
        $result->data->page->totalNum = $sqlresult->num_rows;
        $result->data->page->totalPageNum =$result->data->page->totalNum; 
        //print_r($sqlresult->num_rows);
        
        if ($sqlresult->num_rows>0) 
        {            
            if ($result->data->page->totalPageNum>1) 
            {
                $result->data->page->hasNextpage = true;
            }
			
			$n = $_GET["currentPage"]; //It works!
			
			// Loop that updates the content & page based on page number. 
            for ($x = 0; $x < $n; $x++)
            {
                // goes to the correct row
                $row = $sqlresult->fetch_row();
                
                // updates current page count
                $result->data->page->currentPage= $n;
            }
			
			$lastdate = $row[0];
			$list->publishDate = $lastdate;
			
			$sql = "SELECT DATE_FORMAT(publish_date, '%H:%i') as publishtime, id, content FROM intraday_review where date(publish_date) = '$lastdate' order by publish_date DESC";
			
			if ($sqlresult = $mysqli->query($sql))
			{
    			if ($sqlresult->num_rows>0) 
    			{
                    while ($row=$sqlresult->fetch_row())
        			{
        			    $listcontent = new C_listcontent;        
        			    $listcontent->publishTime= $row[0];
        			    $listcontent->id = $row[1];
                        $listcontent->content= $row[2]; 
                        array_push($list->list, $listcontent);
        			}
    			}
			}
        }
    }
    
    array_push($result->data->list,$list);
    $jsonresult=json_encode($result, JSON_UNESCAPED_UNICODE);
    echo urldecode($jsonresult);
    $mysqli->close();
?> 
