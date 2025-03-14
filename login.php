<?php
header('Content-Type: application/json');
include('auth.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['username'], $data['password'])) {
        $username = $data['username'];
        $password = $data['password'];

        if (login($username, $password)) {
            echo json_encode(["message" => "Login successful"]);
        } else {
            http_response_code(401);
            echo json_encode(["error" => "Invalid credentials"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["error" => "Missing username or password"]);
    }
} else {
    http_response_code(405);
    echo json_encode(["error" => "Method Not Allowed"]);
}
?>
