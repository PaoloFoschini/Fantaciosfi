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

$user_id = 1;
$giocate = $betDAO->getUserBets($user_id);
$user = $userDAO->getUser($user_id);
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
    <h1 class="mb-4">Storico scommesse</h1>
    <div class="table-responsive">
      <?php if (count($giocate) > 0): ?>
        <table class="table table-striped table-hover align-middle shadow-sm">
          <thead class="table-dark">
            <tr>
              <th scope="col">Partita</th>
              <th scope="col">Data</th>
              <th scope="col">Scelta</th>
              <th scope="col">Quota</th>
              <th scope="col">Importo</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($giocate as $g):
              $choice = $g["choice"];
            ?>
              <tr>
                <td><?= htmlspecialchars($g["team1"]) ?> vs <?= htmlspecialchars($g["team2"]) ?></td>
                <td><?= htmlspecialchars($g["match_start_date"]) ?></td>
                <td>
                  <?php switch ($choice) {
                    case "W1":
                      $label = htmlspecialchars($g["team1"]) . " vince";
                      $badgeClass = "bg-primary";
                      break;
                    case "W2":
                      $label = htmlspecialchars($g["team2"]) . " vince";
                      $badgeClass = "bg-danger";
                      break;
                    case "X":
                      $label = "Pareggio";
                      $badgeClass = "bg-warning text-dark";
                      break;
                    case "G":
                      $label = "Gol";
                      $badgeClass = "bg-warning text-dark";
                      break;
                    case "NG":
                      $label = "No gol";
                      $badgeClass = "bg-warning text-dark";
                      break;
                    default:
                      $label = "Scelta sconosciuta";
                      $badgeClass = "bg-secondary";
                      break;
                  } ?>
                  <span class="badge <?= $badgeClass ?>"><?= $label ?></span>
                </td>
                <td>
                  <?php
                  $quote = $betDAO->getBetQuotes($g["match_id"]);
                  switch ($choice) {
                    case "W1":
                      $quota = $quote["GW1"];
                      break;
                    case "W2":
                      $quota = $quote["GW2"];
                      break;
                    case "X":
                      $quota = $quote["GX"];
                      break;
                    case "G":
                      $quota = $quote["GG"];
                      break;
                    case "NG":
                      $quota = $quote["GNG"];
                      break;
                    default:
                      $quota = 0;
                      break;
                  }
                  ?>
                  <span class="fw-bold"><?= htmlspecialchars($quota) ?></span>
                </td>
                <td><span class="fw-bold"><?= htmlspecialchars($g["amount"]) ?></span> crediti</td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
    </div>
  <?php else: ?>
    <div class="alert alert-info">Non hai ancora effettuato scommesse.</div>
  <?php endif; ?>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
