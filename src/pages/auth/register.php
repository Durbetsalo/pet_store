<?php
require __DIR__ . '/../../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$fields = [
    "name" => ["type" => "text"],
    "email" => ["type" => "text"],
    "password" => ["type" => "password"],
    "confirm_password" => ["type" => "password"]
];

if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST["verify_code"])) {
    $error = [];
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $confirm_password = trim($_POST["confirm_password"]);

    if (empty($name)) {
        $error["name"] = "Заполните поле";
    }

    if (empty($email)) {
        $error["email"] = "Заполните поле";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error["email"] = "Неверный формат email";
    } else {
        $stmt = $connect->prepare("SELECT * FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error["email"] = "Этот email уже зарегистрирован";
        }
    }

    if (empty($password)) {
        $error["password"] = "Заполните поле";
    }
    if (empty($confirm_password)) {
        $error["confirm_password"] = "Заполните поле";
    }

    if ($password != $confirm_password && !empty($password) && !empty($confirm_password)) {
        $error["confirm_password"] = "Пароли не совпадают";
    }

    if (!empty($error)) {
        $_SESSION["error"] = $error;
        $_SESSION["old_input"] = [
            "name" => $name,
            "email" => $email
        ];
        header("Location: /register");
        exit();
    }


    $code = rand(100000, 999999);
    $_SESSION["email_code"] = $code;
    $_SESSION["pending_registration"] = [
        "name" => $name,
        "email" => $email,
        "password" => $password
    ];

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV["MAIL_USERNAME"];
        $mail->Password   = $_ENV["MAIL_PASSWORD"];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom($_ENV["MAIL_FROM_ADDRESS"], $_ENV["MAIL_FROM_NAME"]);

        $mail->addAddress($email, $name);

        $mail->isHTML(true);
        $mail->Subject = 'Код подтверждения Bloop';
        $mail->Body    = "
        <h2>Привет!</h2>
        <p>Ваш код подтверждения: <b>$code</b></p>
        ";
        $mail->AltBody = "Ваш код подтверждения: $code";
        $mail->send();
        $_SESSION["verify_from"] = "register";
        header("Location: /verify-code");
    } catch (Exception $e) {
        $_SESSION["error"]["general"] = "Ошибка отправки кода. Попробуйте позже.";
        $_SESSION["old_input"] = [
            "name" => $name,
            "email" => $email
        ];
        header("Location: /register");
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

    $pending = $_SESSION["pending_registration"];
    $hashed_password = password_hash($pending["password"], PASSWORD_DEFAULT);
    $img = null;

    $stmt = $connect->prepare("INSERT INTO users (name, email, password, img) VALUES (?,?,?,?)");
    $stmt->bind_param("ssss", $pending["name"], $pending["email"], $hashed_password, $img);
    $stmt->execute();
    $_SESSION["user_id"] = $connect->insert_id;
    $_SESSION["user_role"] = "user";
    $_SESSION["user_avatar"] = null;
    unset(
        $_SESSION["pending_registration"],
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
<main class="auth-page auth-page--register">
    <div class="auth-page__content">
        <a href="/" class="auth-page__logo">Bloop</a>
        <div class="auth-page__inner">
            <h1 class="auth-page__title">Создать аккаунт</h1>
            <?php if (isset($input_error["general"])): ?>
                <span class="auth-page__error auth-page__error--general"><?= $input_error["general"] ?></span>
            <?php endif; ?>
            <form action="" method="POST" class="auth-page__form">
                <div class="auth-page__field <?= isset($input_error["name"]) ? 'auth-page__field--error' : '' ?>">
                    <input type="text" name="name" class="auth-page__input" placeholder="Имя" value="<?= htmlspecialchars($old_input["name"] ?? "") ?>">
                    <?php if (isset($input_error["name"])): ?>
                        <span class="auth-page__error"><?= $input_error["name"] ?></span>
                    <?php endif; ?>
                </div>
                <div class="auth-page__field <?= isset($input_error["email"]) ? 'auth-page__field--error' : '' ?>">
                    <input type="text" placeholder="Почта" name="email" class="auth-page__input" value="<?= htmlspecialchars($old_input["email"] ?? "") ?>">
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
                </div>
                <div class="auth-page__field <?= isset($input_error["confirm_password"]) ? 'auth-page__field--error' : '' ?>">
                    <div class="auth-page__input-wrapper">
                        <input type="password" name="confirm_password" class="auth-page__input auth-page__input--password" placeholder="Подтвердите пароль">
                        <button type="button" class="auth-page__toggle-password" data-target="confirm_password">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                        </button>
                    </div>
                    <?php if (isset($input_error["confirm_password"])): ?>
                        <span class="auth-page__error"><?= $input_error["confirm_password"] ?></span>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn--primary btn--full auth-page__submit">СОЗДАТЬ АККАУНТ</button>
            </form>
            <p class="auth-page__switch">Уже есть аккаунт? <a href="/login" class="auth-page__switch-link">Войти</a></p>
        </div>
    </div>
    <div class="auth-page__image">
        <img src="/public/assets/images/register-dog.png" alt="">
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