<?php
session_start();

// Verifica che l'utente sia loggato
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

include 'components.php';
require_once 'DAO/db.php';
require_once 'DAO/MatchDAO.php';
require_once 'DAO/TeamDAO.php';
require_once 'DAO/BetDAO.php';
require_once 'DAO/UserDAO.php';
$matchDAO = new MatchDAO($pdo);
$userDAO = new UserDAO($pdo);
$betDAO = new BetDAO($pdo);
$teamDAO = new TeamDAO($pdo);

$user = $userDAO->getUser($_SESSION['user']['email']);
$giocate = $betDAO->getUserBets($user['id']);
$balance = $user['balance'];
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Storico scommesse</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <?php createNavbar($balance, 'home', 'openBets', 'storico') ?>;

    <div class="container">
        <div class="table-responsive">
            <?php if (count($giocate) > 0): ?>
                <?php foreach ($giocate as $g):
                    $choice = $g["choice"];
                ?>
                    <div class="card">
                        <div class="card-body">
                            <?= date("d/m/Y H:i", strtotime($g["match_start_date"])) ?>
                            <br>
                            <?= htmlspecialchars($g["league"]) ?>
                            <br>
                            <?= htmlspecialchars($g["team1"]) ?> vs <?= htmlspecialchars($g["team2"]) ?>
                            <p class="card-text">Scelta: <?= htmlspecialchars($choice) ?></p>
                            <p class="card-text">Importo: <?= htmlspecialchars($g["amount"]) ?></p>
                        </div>
                    </div>

                <?php endforeach; ?>
                </tbody>

        </div>
    <?php else: ?>
        <div class="alert alert-info">Non hai ancora effettuato scommesse.</div>
    <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
