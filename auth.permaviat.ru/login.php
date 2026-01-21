<?php
// 1. Разрешаем запросы и работу с куками
header("Access-Control-Allow-Origin: http://security");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

include("./settings/connect_datebase.php");

// 2. Читаем JSON, если FormData по какой-то причине не пришла
$input = json_decode(file_get_contents('php://input'), true);
$login = $_POST['login'] ?? $input['login'] ?? '';
$password = $_POST['password'] ?? $input['password'] ?? '';

if (empty($login)) {
    http_response_code(400);
    echo json_encode(["error" => "Пустой логин"]);
    exit;
}

$query_user = $mysqli->query("SELECT id, roll FROM `users` WHERE `login`='".$login."' AND `password`= '".$password."';");

if ($user = $query_user->fetch_assoc()) {
    // ВАЖНО: Этот ключ должен быть ТАКИМ ЖЕ в jwt_helper.php
    $secret_key = "permaviat"; 
    $issued_at = time();
    $expiration = $issued_at + (60 * 60);

    $payload = [
        "iat" => $issued_at,
        "exp" => $expiration,
        "id"  => $user['id'],
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