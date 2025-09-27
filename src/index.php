<?php
require_once __DIR__ . '/../vendor/autoload.php';

session_start();

$client = new Google_Client();
$client->setAuthConfig(__DIR__ . '/../client_secret.json');
$client->addScope("email");
$client->addScope("profile");

$authUrl = $client->createAuthUrl();
?>

<!doctype html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <title>Login con Google</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex justify-content-center align-items-center vh-100">
    <div class="text-center">
        <h1 class="mb-4">Accedi con Google</h1>
        <a href="<?= htmlspecialchars($authUrl) ?>" class="btn btn-danger btn-lg">
            <i class="bi bi-google"></i> Login con Google
        </a>
    </div>
</body>
</html>
