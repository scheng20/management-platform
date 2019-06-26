<?php

    header('Content-Type: application/json');
    require_once $_SERVER['DOCUMENT_ROOT'] .'/source/config.php';
    
    class C_result 
    {
        public $data = "";
        public $error_code= 0;
        public $error_msg="SUCCESS";
    }
    
    $res = new C_result;
    $content = "";
    
    // check if something is being posted
    if (isset($_POST['content']))
    {
        $content = $_POST['content']; 
    }
    else
    {
        $res->error_code = 5004;
        $res->error_msg = "Reply Cannot Be Empty!";
    }
    
    $replyToID = $_POST['id'];
    
    $timestamp = time();
    $date_and_time = date("Y-m-d h:i:s", $timestamp);
    $isRecommend = 0;
    
    $sqlRecommend = "SELECT is_recommend from user_comment WHERE id = $replyToID";
    
    if ($sqlresult = $mysqli->query($sqlRecommend))
    {
        if ($sqlresult->num_rows>0) 
    	{
            $row=$sqlresult->fetch_row();
            $isRecommend = $row[0];
    	}
    }
    
    // Double check that the content isn't blank 
    if ($content == "" || $content == " ")
    {
        $res->error_code = 5004;
        $res->error_msg = "Reply Cannot Be Empty!";
    }
    else
    {
        $stmt = $mysqli->prepare("INSERT INTO user_comment (content, user_id, pid, status, type, is_recommend, like_num, updated_at, created_at) 
VALUES (CONCAT(': ', ? ), 3, $replyToID, 1, 3, $isRecommend, 0, '$date_and_time', '$date_and_time')");
        
        if ($stmt === false) 
        {
            error_log('mysqli statement prepare error:'.$mysqli->error);
        }
        else
        {
            $stmt->bind_param("s", $content);
            $stmt->execute();
            $stmt->close();
        }
        
        $res->error_code = 0;
        $res->error_msg = "SUCCESS";

    }
    
    $jsonresult=json_encode($res, JSON_UNESCAPED_UNICODE);
    echo urldecode($jsonresult);
    $mysqli->close();

?>
