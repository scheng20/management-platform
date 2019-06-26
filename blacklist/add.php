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
    $data = "";
    
    // Get the date and time
    $timestamp = time();
    $date_and_time = date("Y-m-d h:i:s", $timestamp);
    
    if (isset($_POST['name']))
    {
        $data = $_POST['name'];
        $stmt = $mysqli->prepare("INSERT INTO key_words (name, status, updated_at, created_at) VALUES ( ? , 1, '$date_and_time', '$date_and_time')");

        if ($stmt === false) 
        {
            error_log('mysqli statement prepare error:'.$mysqli->error);
        }
        else
        {
            $stmt->bind_param("s", $data);
            $stmt->execute();
        
            $res->error_code= 0;
            $res->error_msg="SUCCESS";
        }

         $stmt->close();
        
    }
    else
    {
        $res->error_code= 5006;
        $res->error_msg="Field Cannot Be Empty!";
    }
    
    $jsonresult=json_encode($res, JSON_UNESCAPED_UNICODE);
    echo urldecode($jsonresult);
    $mysqli->close();
?>
