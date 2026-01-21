<?php
header("Access-Control-Allow-Origin: https://security.permaviat.ru");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

include("./settings/connect_datebase.php");

$login = $_POST['login'] ?? '';
$password = $_POST['password'] ?? '';

$query_user = $mysqli->query("SELECT id, role FROM `users` WHERE `login`='".$login."' AND `password`= '".$password."';");

if ($user = $query_user->fetch_assoc()) {

    $secret_key = "YOUR_SUPER_SECRET_KEY"; 
    $issued_at = time();
    $expiration = $issued_at + (60 * 60);

    $payload = [
        "iat" => $issued_at,
        "exp" => $expiration,
        "sub" => $user['id'],
        "roll" => $user['roll']
    ];

    function base64UrlEncode($data) {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
    }

    $header = base64UrlEncode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
    $payload_encoded = base64UrlEncode(json_encode($payload));
    $signature = base64UrlEncode(hash_hmac('sha256', "$header.$payload_encoded", $secret_key, true));

    $jwt = "$header.$payload_encoded.$signature";
    
    echo json_encode(["token" => $jwt]);
} else {
    http_response_code(401);
    echo json_encode(["error" => "Неверный логин или пароль"]);
}
?>