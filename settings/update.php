<?php 

    header('Content-Type: application/json');
    require_once $_SERVER['DOCUMENT_ROOT'] .'/source/config.php';
    
    class C_result 
    {
        public $data = "";
        public $error_code=0;
        public $error_msg="SUCCESS";
    }
    
    $id = $_POST['id'];
    $value = $_POST['value'];
    
    $sql = "UPDATE sys_config SET value = $value WHERE id = $id";
    
    if ($sqlresult = $mysqli->query($sql))
    {
        $res = new C_result;
        $jsonresult=json_encode($res, JSON_UNESCAPED_UNICODE);
        echo urldecode($jsonresult);
    } 
    
    $mysqli->close();

?>
