<?php
setcookie("token", "", time() - 3600, "/", ".permaviat.ru", true, true);
setcookie("token", "", time() - 3600, "/");

session_start();
session_unset();
session_destroy();

echo json_encode(["status" => "success"]);
?>