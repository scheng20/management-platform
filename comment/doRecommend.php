<?php
    
    header('Content-Type: application/json');
    require_once $_SERVER['DOCUMENT_ROOT'] .'/source/config.php';
    
    class C_result 
    {
        public $data = "";
        public $error_code= 0; 
        public $error_msg="SUCCESS";
    }
    
    $ID = $_POST['id'];
    
    // Get the current recommend status of the thread
    $sql_current = "SELECT is_recommend FROM user_comment WHERE id = $ID";
    
    if ($cResult = $mysqli->query($sql_current))
    {
        if ($cResult->num_rows>0) 
    	{
    	    $row = $cResult->fetch_row();
            $current = $row[0];
            
            if ($current == 0)
            {
                $sql ="UPDATE user_comment SET is_recommend = 1 WHERE id = $ID";
            }
            else
            {
                $sql ="UPDATE user_comment SET is_recommend = 0 WHERE id = $ID";
            }
            
            if ($sqlresult = $mysqli->query($sql))
            {
                $res = new C_result;
                $jsonresult=json_encode($res, JSON_UNESCAPED_UNICODE);
                echo urldecode($jsonresult);
            }
    	}
    }
    
    $mysqli->close();

?>
