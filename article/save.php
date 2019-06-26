<?php

    header('Content-Type: application/json');
    require_once $_SERVER['DOCUMENT_ROOT'] .'/source/config.php';
    
    class C_result 
    {
        public $error_code= 0 ;
        public $error_msg='SUCCESS';
    }
    
    $id = $_POST['id'];
    $type = $_POST['type']; 
    $title = $_POST['title'];
    $author = $_POST['author'];
    $content = $_POST['content'];
    $info = $_POST['info'];
    
    //Note: type above determines the type of article:
    // 1 = Published
    // 2 = Draft
    
    // We need to get the current time
    
    date_default_timezone_set('Asia/Shanghai');
    $date = date('Y-m-d h:i:s', time());
    
    if ($id == "NULL")
    {
        // This has no ID, meaning that just simply add a new article entry to the database. 
        
        $createdAt = $date;
        $updatedAt = $date;
        
        $stmt = $mysqli->prepare("INSERT INTO article (name, author, intro, content, user_id, status, type, updated_at, created_at, read_cnt)
                VALUES (?, ?, ?, ?, 4, 0, $type, '$createdAt', '$updatedAt', 0)");
                
        if ($stmt === false) 
        {
            error_log('mysqli statement prepare error:'.$mysqli->error);
        }
        else
        {
            $stmt->bind_param("ssss", $title, $author, $info, $content );
            $stmt->execute();
        }
        
        $stmt->close();
    }
    else
    {
        // This has ID, meaning that it is editing an existing article, use UPDATE in the database. 
        
        $updatedAt = $date;
        
        $stmt = $mysqli->prepare("UPDATE article
                SET name = ?, author = ?, intro = ?,
                content = ?, type = '$type', updated_at = '$updatedAt'
                WHERE id = $id");
                
        if ($stmt === false) 
        {
            error_log('mysqli statement prepare error:'.$mysqli->error);
        }
        else
        {
            $stmt->bind_param("ssss", $title, $author, $info, $content );
            $stmt->execute();
        }
        
        $stmt->close();
        
    }
    
    $res = new C_result;
    $jsonresult=json_encode($res, JSON_UNESCAPED_UNICODE);
    echo urldecode($jsonresult);
    
    $mysqli->close();

?>
