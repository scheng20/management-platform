<?php

    header('Content-Type: application/json');
    require_once $_SERVER['DOCUMENT_ROOT'] .'/source/config.php';
    
    $id = $_GET['id'];
    
    class C_result 
    {
        public $data;
        public $error_code=0;
        public $error_msg='SUCCESS';
    }
    
    class C_data
    {
        public $author;
        public $title;
        public $content;
    }
    
    $sql = "SELECT name, author, content FROM article WHERE id = $id";
    
    if ($sqlresult = $mysqli->query($sql))
    {
        $res = new C_result;
        $res->data = new C_data;
        
        if ($sqlresult->num_rows>0) 
        {
            $row = $sqlresult->fetch_row();
            
            $res->data->title = $row[0];
            $res->data->author = $row[1];
            $res->data->content = $row[2];
        }
        else
        {
            $res->data->title = "";
            $res->data->author = "";
            $res->data->content = "";
        }
        
        $jsonresult=json_encode($res, JSON_UNESCAPED_UNICODE);
        echo urldecode($jsonresult);
    } 
    
    $mysqli->close();
?>
