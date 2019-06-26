<?php

    header('Content-Type: application/json');
    require_once $_SERVER['DOCUMENT_ROOT'] .'/source/config.php';
    
    class C_result 
    {
        public $data = "";
        public $error_code= 0; // May need to change this
        public $error_msg="SUCCESS";
    }
    
    $ID = $_POST['id'];
    
    $sql = "DELETE FROM user_comment WHERE pid = $ID OR id = $ID";
    
    if ($sqlresult = $mysqli->query($sql))
    {
        $res = new C_result;
        $jsonresult=json_encode($res, JSON_UNESCAPED_UNICODE);
        echo urldecode($jsonresult);
    }
    
    $mysqli->close();

?>
