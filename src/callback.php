<?php
require_once __DIR__ . '/../vendor/autoload.php';

session_start();

$client = new Google_Client();
$client->setAuthConfig(__DIR__ . '/../client_secret.json');

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    if (!isset($token['error'])) {
        $client->setAccessToken($token['access_token']);

        // Ottieni info utente
        $oauth = new Google_Service_Oauth2($client);
        $userData = $oauth->userinfo->get();

        // Salva in sessione
        $_SESSION['user'] = [
            'name' => $userData->name,
            'email' => $userData->email,
            'picture' => $userData->picture
        ];
	header("Location: home.php");
	exit;
    } else {
        echo "Errore nel login: " . htmlspecialchars($token['error']);
        exit;
    }
} else {
    echo "Codice di autorizzazione mancante.";
    exit;
}

$user = $_SESSION['user'];
?>

<!doctype html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <title>Benvenuto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex justify-content-center align-items-center vh-100">
    <div class="card text-center p-4">
        <img src="<?= htmlspecialchars($user['picture']) ?>" class="rounded-circle mx-auto mb-3" width="100" alt="Avatar">
        <h3>Ciao, <?= htmlspecialchars($user['name']) ?>!</h3>
        <p class="text-muted"><?= htmlspecialchars($user['email']) ?></p>
        <a href="logout.php" class="btn btn-outline-secondary mt-3">Logout</a>
    </div>
</body>
</html>
