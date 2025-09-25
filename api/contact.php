<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        require('./config.php');
        $dbcon = new mysqli(DB_SERVERNAME, DB_USERNAME, DB_PASS, DB_NAME);
        if ($dbcon->connect_error) {
            throw new Exception("DB connect error.", 500);
        }

        $input = file_get_contents("php://input");
        $data = json_decode($input, true);

        $name  = $data['name'] ?? '';
        $email = $data['email'] ?? '';
        $msg   = $data['msg'] ?? '';

        // DBに保存
        $stmt = $dbcon->prepare("INSERT INTO contacts (name, email, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $msg);


        if ($stmt->execute()) {
            echo json_encode([
                "status" => "success",
                "message" => "Data inserted successfully",
                "received" => [
                    "name" => $name,
                    "email" => $email,
                    "msg" => $msg
                ]
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Insert failed: " . $stmt->error
            ]);
        }

        $stmt->close();
        $dbcon->close();
        exit;
    } catch (Exception $err) {
        http_response_code($err->getCode() ?: 500);
        echo json_encode([
            "status" => "error",
            "message" => $err->getMessage()
        ]);
    }
}
