<?php

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

require_once __DIR__ . '/../config/database.php';
require_once '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'ok' => false,
        'error' => 'method_not_allowed'
    ]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['email'], $input['password'], $input['captcha_token'])) {
    echo json_encode([
        'ok' => false,
        'error' => 'missing_fields',
        'msg' => 'Campos incompletos.'
    ]);
    exit;
}

$email = trim($input['email']);
$password = $input['password'];
$captchaToken = $input['captcha_token'];

if (!verificarTokenHumano($captchaToken)) {
    echo json_encode([
        'ok' => false,
        'error' => 'captcha_failed',
        'msg' => 'Validación humana fallida.'
    ]);
    exit;
}

try {
    $stmt = $pdo->prepare(
        'SELECT id, nombre, email, password_hash
         FROM usuarios
         WHERE email = ?'
    );

    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = htmlspecialchars(
            $user['nombre'],
            ENT_QUOTES,
            'UTF-8'
        );
        $_SESSION['user_email'] = htmlspecialchars(
            $user['email'],
            ENT_QUOTES,
            'UTF-8'
        );

        echo json_encode([
            'ok' => true,
            'msg' => 'Acceso concedido',
            'user' => [
                'id' => $_SESSION['user_id'],
                'name' => $_SESSION['user_name'],
                'email' => $_SESSION['user_email']
            ]
        ]);
    } else {
        echo json_encode([
            'ok' => false,
            'error' => 'invalid_credentials',
            'msg' => 'Correo o contraseña incorrectos.'
        ]);
    }

} catch (PDOException $e) {

    error_log('Error de login: ' . $e->getMessage());

    echo json_encode([
        'ok' => false,
        'error' => 'server_error',
        'msg' => 'Error interno de autenticación.'
    ]);
}
?>