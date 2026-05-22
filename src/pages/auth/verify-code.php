<?php
$from = $_SESSION["verify_from"] ?? '';

if(!in_array($from, ["register", "login"])){
    header("Location: /");
    exit;
}

$email = $_SESSION["pending_registration"]["email"] ?? $_SESSION["login_email"] ?? 'example@example.com';

$input_error = $_SESSION["error"] ?? [];

unset($_SESSION["error"]);
?>
<main class="verify-page">
    <div class="verify-page__content">
        <a href="/" class="verify-page__logo">Bloop</a>
        <div class="verify-page__inner">
            <h1 class="verify-page__title">Подтвердите почту</h1>
            <p class="verify-page__text">Мы отправили письмо с кодом на <strong><?= htmlspecialchars($email) ?></strong> <a href="/<?= $from ?>" class="verify-page__change">(изменить)</a>.<br>Не забудьте проверить папку "Спам".</p>
            <form action="/<?= $from ?>" method="POST" class="verify-page__form" id="verifyForm">
                <div class="verify-page__code <?= isset($input_error["verify_code"]) ? 'verify-page__code--error' : '' ?>" id="codeInputs">
                    <input type="text" name="verify_code[]" maxlength="1" class="verify-page__digit" inputmode="numeric" pattern="[0-9]" autocomplete="one-time-code">
                    <input type="text" name="verify_code[]" maxlength="1" class="verify-page__digit" inputmode="numeric" pattern="[0-9]">
                    <input type="text" name="verify_code[]" maxlength="1" class="verify-page__digit" inputmode="numeric" pattern="[0-9]">
                    <input type="text" name="verify_code[]" maxlength="1" class="verify-page__digit" inputmode="numeric" pattern="[0-9]">
                    <input type="text" name="verify_code[]" maxlength="1" class="verify-page__digit" inputmode="numeric" pattern="[0-9]">
                    <input type="text" name="verify_code[]" maxlength="1" class="verify-page__digit" inputmode="numeric" pattern="[0-9]">
                </div>
                <input type="hidden" name="verify_code" id="verifyCodeValue">
                <?php if (isset($input_error["verify_code"])): ?>
                    <span class="verify-page__error"><?= $input_error["verify_code"] ?></span>
                <?php endif; ?>
                <button type="submit" class="btn btn--primary btn--full verify-page__submit">ПОДТВЕРДИТЬ</button>
            </form>
        </div>
    </div>
    <div class="verify-page__image">
        <img src="/public/assets/images/verify-parrot.png" alt="">
    </div>
</main>

<script>
    (function() {
        var inputs = document.querySelectorAll('.verify-page__digit');
        var hiddenInput = document.getElementById('verifyCodeValue');
        var form = document.getElementById('verifyForm');

        inputs.forEach(function(input, index) {
            input.addEventListener('input', function(e) {
                var value = this.value.replace(/[^0-9]/g, '');
                this.value = value;
                if (value && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
                updateHiddenValue();
            });

            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && !this.value && index > 0) {
                    inputs[index - 1].focus();
                }
            });

            input.addEventListener('paste', function(e) {
                e.preventDefault();
                var paste = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '');
                for (var i = 0; i < Math.min(paste.length, inputs.length); i++) {
                    inputs[i].value = paste[i];
                }
                var focusIndex = Math.min(paste.length, inputs.length) - 1;
                inputs[focusIndex].focus();
                updateHiddenValue();
            });
        });

        function updateHiddenValue() {
            var code = '';
            inputs.forEach(function(input) {
                code += input.value;
            });
            hiddenInput.value = code;
        }

        form.addEventListener('submit', function() {
            updateHiddenValue();
        });
    })();
</script>
