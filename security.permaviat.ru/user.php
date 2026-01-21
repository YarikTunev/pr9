<?php
    include("./settings/jwt_helper.php");
    include("./settings/connect_datebase.php");
    
    // Получаем данные ТОЛЬКО из JWT
    $user = get_user_from_jwt();

    if (!$user) {
        // Если токена нет, уходим на логин
        header("Location: login.php");
        exit;
    }

    // Если это админ, перекидываем в админку
    if (isset($user['roll']) && $user['roll'] == 1) {
        header("Location: admin.php");
        exit;
    }

    // Сохраняем ID для дальнейшего использования на странице
    $current_user_id = $user['id']; 
?>
<!DOCTYPE HTML>
<html>
    <head>
        <script src="https://code.jquery.com/jquery-1.8.3.js"></script>
        <meta charset="utf-8">
        <title> Личный кабинет </title>
        <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <div class="top-menu">
            <a href="#" class="singin"><img src="img/ic-login.png"/></a>
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
                <input type="button" class="button" value="Выйти" onclick="logout()"/>
                <div class="name" style="padding-bottom: 0px;">Личный кабинет</div>
                <div class="description">
                    Добро пожаловать:
                    <?php
                        $current_user_id = $user['id'];

                        // Получаем актуальное имя из базы
                        $stmt = $mysqli->prepare("SELECT login FROM `users` WHERE `id` = ?");
                        $stmt->bind_param("i", $current_user_id);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $user_data = $result->fetch_assoc();

                        if ($user_data) {
                            echo htmlspecialchars($user_data['login']);
                        } else {
                            echo "Пользователь";
                        }
                    ?>
                    <br>Ваш идентификатор:
                    <?php echo htmlspecialchars($current_user_id); ?>
                </div>
            
                <div class="footer">
                    © КГАПОУ "Авиатехникум", 2020
                    <a href="#">Конфиденциальность</a>
                    <a href="#">Условия</a>
                </div>
            </div>
        </div>
        
        <script>
            function logout() {
			$.ajax({
				url: 'ajax/logout.php',
				type: 'POST',
				xhrFields: {
					withCredentials: true 
				},
				success: function (_data) {
					localStorage.removeItem("token");
					window.location.href = "login.php"; 
				},
				error: function() {
					localStorage.removeItem("token");
					document.cookie = "token=; path=/; expires=Thu, 01 Jan 1970 00:00:01 GMT;";
					window.location.href = "login.php";
				}
			});
		}
        </script>
    </body>
</html>