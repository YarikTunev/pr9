<?php
function get_user_from_jwt() {
    $secret_key = "YOUR_SUPER_SECRET_KEY";
    if (!isset($_COOKIE['token'])) {
        return null;
    }

    $jwt = $_COOKIE['token'];
    $tokenParts = explode('.', $jwt);
    
    if (count($tokenParts) != 3) return null;

    $header = $tokenParts[0];
    $payload = $tokenParts[1];
    $signature_provided = $tokenParts[2];

    $base64_header_payload = $header . '.' . $payload;
    $signature_expected = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(hash_hmac('sha256', $base64_header_payload, $secret_key, true)));

    if ($signature_expected === $signature_provided) {
        $data = json_decode(base64_decode($payload), true);
        if (isset($data['exp']) && $data['exp'] > time()) {
            return $data;
        }
    }

    return null;
}
?>