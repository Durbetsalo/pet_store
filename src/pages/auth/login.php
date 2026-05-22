<?php
require __DIR__ . '/../../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST["verify_code"])) {
    $error = [];
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if (empty($email)) {
        $error["email"] = "Заполните поле";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error["email"] = "Неверный формат email";
    }

    if (empty($password)) {
        $error["password"] = "Заполните поле";
    }

    if (!empty($error)) {
        $_SESSION["error"] = $error;
        $_SESSION["old_input"] = [
            "email" => $email
        ];
        header("Location: /login");
        exit();
    }

    $stmt = $connect->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_array(MYSQLI_ASSOC);

    if (!$user || !password_verify($password, $user["password"])) {
        $_SESSION["error"]["general"] = "неверный логин или пароль";
        $_SESSION["old_input"] = [
            "email" => $email
        ];
        header("Location: /login");
        exit();
    }

    if (in_array($user["role"], ["admin", "manager"])) {
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["user_role"] = $user["role"];
        $_SESSION["user_avatar"] = $user["img"];
        header("Location: /");
        exit;
    }


    //отправка письма
    $_SESSION["pending_user"] = [
        "id" => $user["id"],
        "img" => $user["img"],
        "role" => $user["role"]
    ];
    $_SESSION["login_email"] = $email;
    $code = rand(100000, 999999);
    $_SESSION["email_code"] = $code;

    $mail = new PHPMailer(true);

    try {
        // Настройки сервера
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV["MAIL_USERNAME"];
        $mail->Password   = $_ENV["MAIL_PASSWORD"];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        // Отправитель
        $mail->setFrom($_ENV["MAIL_FROM_ADDRESS"], $_ENV["MAIL_FROM_NAME"]);

        // Получатель
        $mail->addAddress($email, $user["name"]);

        // Контент письма
        $mail->isHTML(true);
        $mail->Subject = 'Код подтверждения Bloop';
        $mail->Body    = "
        <h2>Привет!</h2>
        <p>Ваш код подтверждения: <b>$code</b></p>
        ";
        $mail->AltBody = "Ваш код подтверждения: $code";
        $mail->send();
        $_SESSION["verify_from"] = "login";
        header("Location: /verify-code");
    } catch (Exception $e) {
        $_SESSION["error"]["general"] = "Ошибка отправки кода. Попробуйте позже.";
        $_SESSION["old_input"] = [
            "password" => $password,
            "email" => $email
        ];
        header("Location: /login");
        exit();
    }
}



if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["verify_code"])) {
    $error = [];
    $user_code = trim($_POST["verify_code"]);

    if (empty($user_code)) {
        $error["verify_code"] = "Введите код";
    } else if ($user_code !== (string)$_SESSION["email_code"]) {
        $error["verify_code"] = "Неверный код. Попробуйте еще раз";
    }

    if (!empty($error)) {
        $_SESSION["error"] = $error;
        header("Location: /verify-code");
        exit();
    }

    $_SESSION["user_id"] = $_SESSION["pending_user"]["id"];
    $_SESSION["user_role"] = $_SESSION["pending_user"]["role"];
    $_SESSION["user_avatar"] = $_SESSION["pending_user"]["img"];
    unset(
        $_SESSION["pending_user"],
        $_SESSION["email_code"],
        $_SESSION["verify_from"],
    );
    header("Location: /");
    exit;
}



$input_error = $_SESSION["error"] ?? [];
$old_input = $_SESSION["old_input"] ?? [];

unset($_SESSION["error"]);
unset($_SESSION["old_input"]);
?>
<main class="auth-page auth-page--login">
    <div class="auth-page__content">
        <a href="/" class="auth-page__logo">Bloop</a>
        <div class="auth-page__inner">
            <h1 class="auth-page__title">С возвращением!</h1>
            <?php if (isset($input_error["general"])): ?>
                <span class="auth-page__error auth-page__error--general"><?= $input_error["general"] ?></span>
            <?php endif; ?>
            <form action="" method="POST" class="auth-page__form">
                <div class="auth-page__field <?= isset($input_error["email"]) ? 'auth-page__field--error' : '' ?>">
                    <input type="text" name="email" placeholder="Почта" class="auth-page__input" value="<?= htmlspecialchars($old_input["email"] ?? "") ?>">
                    <?php if (isset($input_error["email"])): ?>
                        <span class="auth-page__error"><?= $input_error["email"] ?></span>
                    <?php endif; ?>
                </div>
                <div class="auth-page__field <?= isset($input_error["password"]) ? 'auth-page__field--error' : '' ?>">
                    <div class="auth-page__input-wrapper">
                        <input type="password" name="password" class="auth-page__input auth-page__input--password" placeholder="Пароль">
                        <button type="button" class="auth-page__toggle-password" data-target="password">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                        </button>
                    </div>
                    <?php if (isset($input_error["password"])): ?>
                        <span class="auth-page__error"><?= $input_error["password"] ?></span>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn--primary btn--full auth-page__submit">ВОЙТИ</button>
            </form>
            <p class="auth-page__switch">Еще нет аккаунта? <a href="/register" class="auth-page__switch-link">Создать аккаунт</a></p>
        </div>
    </div>
    <div class="auth-page__image">
        <img src="/public/assets/images/login-cat.png" alt="">
    </div>
</main>

<script>
    document.querySelectorAll('.auth-page__toggle-password').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var targetId = this.getAttribute('data-target');
            var input = document.querySelector('input[name="' + targetId + '"]');
            var isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';

            var svg = this.querySelector('svg');
            if (isPassword) {
                svg.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>';
            } else {
                svg.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
            }
        });
    });
</script>