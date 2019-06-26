<?php 

    header('Content-Type: application/json');
    require_once $_SERVER['DOCUMENT_ROOT'] .'/source/config.php';
    
    class C_result 
    {
        public $data;
        public $error_code = 0;
        public $error_msg = 'SUCCESS';
    }
    
    $content = $_POST['content'];
    $imgURL = $_POST['imgUrl'];
    
    date_default_timezone_set('Asia/Shanghai');
    $date = date('Y-m-d h:i:s', time());
    
    if ($content == "")
    {
        $res = new C_result;
        $res->error_code = 5001;
        $res->error_msg = "Field Cannot Be Empty!";
        $jsonresult=json_encode($res, JSON_UNESCAPED_UNICODE);
        echo urldecode($jsonresult);
    }
    else
    {
        $stmt = $mysqli->prepare("INSERT INTO intraday_review (user_id, content, img_url, status, like_num, publish_date, updated_at, created_at)
        VALUES (3, ? , '$imgURL', 1, 0, '$date', '$date', '$date')");
     
        if ($stmt === false)
        {
            error_log('mysqli statement prepare error:'.$mysqli->error);

        }
        else
        {
            $stmt->bind_param("s", $content);
            $stmt->execute();
            $res = new C_result;
            $jsonresult=json_encode($res, JSON_UNESCAPED_UNICODE);
            echo urldecode($jsonresult);
        }
        
        // close statement
        $stmt->close();
        
    }
    
    // close the connection
    $mysqli->close();
?>
