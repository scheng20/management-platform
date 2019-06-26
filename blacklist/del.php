<?php 
    header('Content-Type: application/json');
    require_once $_SERVER['DOCUMENT_ROOT'] .'/source/config.php';
    
    class C_result 
    {
        public $error_code=0;
        public $error_msg='SUCCESS';
    }
    
    $sql = 'DELETE FROM key_words WHERE id = '.$_POST['id'];
    //error_log($sql . "\n", 3, "del.log");

    if ($sqlresult = $mysqli->query($sql))
    {
        $res = new C_result;
        $jsonresult=json_encode($res, JSON_UNESCAPED_UNICODE);
        echo urldecode($jsonresult);
    }
    
    $mysqli->close();
?>
