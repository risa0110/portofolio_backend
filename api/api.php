<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

#jsonファイルへの保存
function add_json(){
    try{
        require('./config.php');
        $dbcon = new mysqli(DB_SERVERNAME, DB_USERNAME, DB_PASS, DB_NAME);
        if($dbcon->connect_error){
            throw new Exception("DB connect error.");
        }
        
        $insertPrep = $dbcon->prepare("SELECT * FROM myprojects");
        $insertPrep->execute();
        $result=$insertPrep->get_result();
        $products=[];
        while($row = $result->fetch_assoc()){
            $products[] = $row;
        }
        echo json_encode($products);#ここでブラウザ上にjson表示をすることで、フロント側はapi.phpからデータをそのまま取得可能！
        $insertPrep->close();
    }catch(Exception $err){
        http_response_code($err->getCode());
    }
    
}


add_json();

?>