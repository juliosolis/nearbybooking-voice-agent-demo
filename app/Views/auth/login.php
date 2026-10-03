<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="theme-color" content="#173f3b">
    <meta name="description" content="Acceso al panel de demostración de Mirador Azul Voice Concierge.">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="alternate icon" type="image/x-icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="stylesheet" href="/assets/login.css">
    <title>Acceso | Mirador Azul</title>
</head>
<body>
    <main class="login-shell">
        <div class="scene" aria-hidden="true"><span>MIRADOR AZUL / THE COAST</span></div>
        <div class="form-panel">
            <a class="brand" href="/"><span class="emblem">M</span><span>MIRADOR AZUL<small>VOICE OPERATIONS</small></span></a>
            <div class="form-content">
                <p class="eyebrow">ACCESO PRIVADO</p>
                <h1>Bienvenido de nuevo.</h1>
                <p class="intro">Ingresa para revisar llamadas, solicitudes y respuestas del concierge.</p>
                <?php if (session('error') !== null || session('errors') !== null): ?>
                    <div class="alert" role="alert"><?= esc(is_array(session('errors')) ? implode(' ', session('errors')) : (session('error') ?? session('errors'))) ?></div>
                <?php endif ?>
                <form action="<?= url_to('login') ?>" method="post">
                    <?= csrf_field() ?>
                    <label for="email">Correo electrónico</label>
                    <input id="email" name="email" type="email" autocomplete="email" value="<?= esc(old('email')) ?>" required>
                    <label for="password">Contraseña</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required>
                    <button type="submit">Entrar al panel <span>↗</span></button>
                </form>
            </div>
            <p class="disclaimer">Proyecto de portafolio · hotel y datos ficticios</p>
        </div>
    </main>
</body>
</html>
