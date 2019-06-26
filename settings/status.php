<?php 
    header('Content-Type: application/json');
    require_once $_SERVER['DOCUMENT_ROOT'] .'/source/config.php';
    
    class C_result 
    {
        public $data = "";
        public $error_code=0;
        public $error_msg='SUCCESS';
    }
    
    // Get the current status of the option
    $sql_current = 'SELECT status FROM sys_config WHERE id ='.$_POST['id'];
    
    $cResult = $mysqli->query($sql_current);
    
    $row = $cResult->fetch_row();
    $current = $row[0];
    //error_log("The current status is: ".$current . "\n", 3, "status.log");
    
    // Depending on what the current status is, reverse it (switch-like logic)
    if ($current == 1)
    {
        $sql = 'UPDATE sys_config SET status = 2 WHERE id = '.$_POST['id'];
    }
    else if ($current == 2)
    {
        $sql = 'UPDATE sys_config SET status = 1 WHERE id = '.$_POST['id'];
    }

    if ($sqlresult = $mysqli->query($sql))
    {
        $res = new C_result;
        $jsonresult=json_encode($res, JSON_UNESCAPED_UNICODE);
        echo urldecode($jsonresult);
    }
    
    $mysqli->close();
?>
