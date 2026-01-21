<?php
    // УБИРАЕМ session_start() - она больше не нужна для JWT
    include("./settings/jwt_helper.php");
    include("./settings/connect_datebase.php");
    
    // Проверяем, авторизован ли уже пользователь по JWT (из куки)
    $user = get_user_from_jwt();
    
    if ($user) {
        // Если залогинен, сразу перекидываем в нужный кабинет
        if (isset($user['roll']) && $user['roll'] == 1) {
            header("Location: admin.php");
        } else {
            header("Location: user.php");
        }
        exit;
    }
?>
<!DOCTYPE HTML>
<html>
    <head> 
        <meta charset="utf-8">
        <title> Авторизация </title>
        <script src="https://code.jquery.com/jquery-1.8.3.js"></script>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <div class="top-menu">
            <a href="#"><img src="img/logo1.png"/></a>
            <div class="name">
                <a href="index.php">
                    <div class="subname">БЕЗОПАСНОСТЬ ВЕБ-ПРИЛОЖЕНИЙ</div>
                    Пермский авиационный техникум им. А. Д. Швецова
                </a>
            </div>
        </div>
        <div class="space"> </div>
        <div class="main">
            <div class="content">
                <div class="login">
                    <div class="name">Авторизация</div>
                
                    <div class="sub-name">Логин:</div>
                    <input name="_login" type="text" onkeypress="return PressToEnter(event)"/>
                    <div class="sub-name">Пароль:</div>
                    <input name="_password" type="password" onkeypress="return PressToEnter(event)"/>
                    
                    <a href="regin.php">Регистрация</a>
                    <br><a href="recovery.php">Забыли пароль?</a>
                    <input type="button" class="button" value="Войти" onclick="LogIn()"/>
                    <img src="img/loading.gif" class="loading" style="display:none;"/>
                </div>
                
                <div class="footer">
                    © КГАПОУ "Авиатехникум", 2020
                </div>
            </div>
        </div>
        
        <script>
            function LogIn() {
                var loading = $(".loading");
                var button = $(".button");
                
                var _login = $("[name='_login']").val();
                var _password = $("[name='_password']").val();
                
                if (!_login || !_password) {
                    alert("Заполните все поля");
                    return;
                }

                loading.show();
                button.attr('class', 'button_diactive');
                
                var data = new FormData();
                data.append("login", _login);
                data.append("password", _password);
                
                $.ajax({
                   url: 'http://auth/login.php',
					type: 'POST',
					data: data,
					processData: false,
					contentType: false,
					xhrFields: {
						withCredentials: true // КРИТИЧНО для работы куки между доменами
					},
                    success: function (_data) {
                        try {
                            var response = (typeof _data === 'object') ? _data : JSON.parse(_data);
                            
                            if(response.token) {
                                // Сохраняем токен в куки для PHP и в localStorage для JS
                                document.cookie = "token=" + response.token + "; path=/; max-age=3600; SameSite=Lax";
                                localStorage.setItem("token", response.token);
                                
                                // Перенаправляем на проверку (она сама раскидает по ролям)
                                window.location.href = "user.php"; 
                            } else {
                                alert("Ошибка: сервер не вернул токен.");
                                resetUI();
                            }
                        } catch (e) {
                            alert("Неверный логин или пароль");
                            resetUI();
                        }
                    },
                    error: function() {
                        alert("Ошибка связи с сервером авторизации");
                        resetUI();
                    }
                });

                function resetUI() {
                    loading.hide();
                    button.attr('class', 'button');
                }
            }
            
            function PressToEnter(e) {
                if (e.keyCode == 13) LogIn();
            }
        </script>
    </body>
</html>