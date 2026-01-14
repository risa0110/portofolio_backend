<?php
require('./api/config.php');
#データを追加するための設定
$targetDir = __DIR__ . "/uploads/";#pathを絶対にここの制作フォルダのuploadsにしろよ？っていう絶対パスの指定部分

try {
    if (!isset($_POST['new_project'])) {
        throw new Exception("New project couldn't send.");
    }
    $dbcon = new mysqli(DB_SERVERNAME, DB_USERNAME, DB_PASS, DB_NAME);
    if ($dbcon->connect_error) {
        throw new Exception("DB connection error.");
    }
    $title = $_POST["title"];
    $img = $_FILES["img_url"];
    $date = date("Y-m-d");
    $explanation = $_POST["explanation"];
    $post_status = $_POST["post_status"];
    $imgName=basename($_FILES["img_url"]["name"]);
    $saveDir=$targetDir . $imgName;

    $insertPrep = $dbcon->prepare("INSERT INTO myprojects( title, img_url, add_date, explanation,post_status) VALUES (?,?,?,?,?)");
    $insertPrep->bind_param("sssss", $title, $imgName, $date, $explanation, $post_status);
    $insertPrep->execute();
    $insertPrep->close();
    if(move_uploaded_file($_FILES["img_url"]["tmp_name"],$saveDir)){
        echo "Upload completed!";
    }else{
        echo "Upload Failed...";
    }
} catch (Exception $err) {
    http_response_code($err->getCode());
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add projects</title>
</head>

<body>
    <h1>Add your new Project</h1>
    <form method="post" action="index.php" enctype="multipart/form-data">
        <input type="text" name="title" placeholder="Project Title" required>
        <input type="file" name="img_url" placeholder="Image file name + file extension" required><!--ここパソコンより選択のボタンのがいいかも...-->
        <input type="text" name="add_date" hidden><!--ここに日付自動入力にしちゃえば、DBにデータ送信日が送られるはず！-->
        <textarea name="explanation" placeholder="Write explanation about this project." required></textarea>
        <input type="text" name="post_status" placeholder="Where do you want to post? (Home, About, Projects)">
        <button type="submit" name="new_project">Submit</button>
    </form>
</body>

</html>