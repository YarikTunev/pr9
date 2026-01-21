<?php
    include("./settings/jwt_helper.php");
    include("./settings/connect_datebase.php");
    
    // Если пользователь уже авторизован, отправляем его в кабинет
    $user = get_user_from_jwt();
    if ($user) {
        if (isset($user['roll']) && $user['roll'] == 1) header("Location: admin.php");
        else header("Location: user.php");
        exit;
    }
?>
<!DOCTYPE HTML>
<html>
    <head> 
        <meta charset="utf-8">
        <title> Регистрация </title>
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
                    <div class="name">Регистрация</div>
                
                    <div class="sub-name">Логин:</div>
                    <input name="_login" type="text" onkeypress="return PressToEnter(event)"/>
                    <div class="sub-name">Пароль:</div>
                    <input name="_password" type="password" onkeypress="return PressToEnter(event)"/>
                    <div class="sub-name">Повторите пароль:</div>
                    <input name="_passwordCopy" type="password" onkeypress="return PressToEnter(event)"/>
                    
                    <a href="login.php">Вернуться</a>
                    <input type="button" class="button" value="Зарегистрироваться" onclick="RegIn()"/>
                    <img src="img/loading.gif" class="loading" style="display:none;"/>
                </div>
                
                <div class="footer">
                    © КГАПОУ "Авиатехникум", 2020
                </div>
            </div>
        </div>
        
        <script>
            function RegIn() {
                var loading = $(".loading");
                var button = $(".button");
                
                var _login = $("[name='_login']").val();
                var _password = $("[name='_password']").val();
                var _passwordCopy = $("[name='_passwordCopy']").val();
                
                if(_login == "") { alert("Введите логин."); return; }
                if(_password == "") { alert("Введите пароль."); return; }
                if(_password != _passwordCopy) { alert("Пароли не совпадают."); return; }

                loading.show();
                button.attr('class', 'button_diactive');
                
                var data = new FormData();
                data.append("login", _login);
                data.append("password", _password);
                
                $.ajax({
                    url         : 'ajax/regin_user.php',
                    type        : 'POST',
                    data        : data,
                    processData : false,
                    contentType : false, 
                    success: function (_data) {
                        // Убираем лишние пробелы из ответа
                        var result = _data.trim(); 
                        
                        if(result == "-1") {
                            alert("Пользователь с таким логином существует.");
                            loading.hide();
                            button.attr('class', 'button');
                        } else {
                            alert("Регистрация успешна! Теперь войдите в систему.");
                            window.location.href = "login.php"; // После рега отправляем на вход
                        }
                    },
                    error: function(){
                        alert('Системная ошибка при регистрации!');
                        loading.hide();
                        button.attr('class', 'button');
                    }
                });
            }
            
            function PressToEnter(e) {
                if (e.keyCode == 13) RegIn();
            }
        </script>
    </body>
</html>