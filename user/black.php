<?php

    header('Content-Type: application/json');
    
    require_once $_SERVER['DOCUMENT_ROOT'] .'/source/config.php';
    
    class C_result 
    {
        public $data = "";
        public $error_code=0;
        public $error_msg='SUCCESS';
    }
    
    // Note the status in the user table means:
    // 1 = Normal
    // 2 = Blacken
    // 3 = Ban Account
    
    // Add a statement that basically reverses whatever state the user is currently in
    $sql = '';
    $sql_current = 'SELECT status FROM user WHERE id ='.$_POST['id'];
    
    $cResult = $mysqli->query($sql_current);
    
    $row = $cResult->fetch_row();
    $current = $row[0];
    
    //error_log($sql_current. "\n",3,"black.log");
    //error_log($current. "\n",3,"black.log");

    if ($current == 1)
    {
        // black
        $sql = 'UPDATE user SET status = 2 WHERE id = '.$_POST['id'];
    }
    else if ($current == 2)
    {
        // unblack
        $sql = 'UPDATE user SET status = 1 WHERE id ='.$_POST['id'];
    }
    
    if ($sqlresult = $mysqli->query($sql))
    {
        $res = new C_result;
        $jsonresult=json_encode($res, JSON_UNESCAPED_UNICODE);
        echo urldecode($jsonresult);
    }
    
    $mysqli->close();

?> 
