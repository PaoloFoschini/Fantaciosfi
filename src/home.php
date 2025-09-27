<?php
session_start();
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
$partite = $matchDAO->getAll();
$user = $userDAO->getUser($user_id);
$balance = $user['balance'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $match_id = $_POST["match_id"];
  $choice = $_POST["choice"];
  $amount = (int)$_POST["amount"];
  if ($balance < $amount) {
    die("Saldo insufficiente!");
  }
  $newBalance = $balance - $amount;
  try {
    $betDAO->addBet($match_id, $user_id, $choice, $amount);
    $userDAO->updateBalance($newBalance, $user_id);
    header("Location: storico.php");
    exit;
  } catch (PDOException $e) {
    $errorMessage = 'Hai già scommesso su questa partita!';
  }
}
?>

<!DOCTYPE html>
<html lang="it">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Homepage - Fantaciosfi</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

  <?php createNavbar($balance, 'index', 'openBets', 'storico') ?>;

  <div class="container">

    <div class="row">
      <?php foreach ($partite as $p): ?>
        <?php $quote = $betDAO->getBetQuotes($p["id"]); ?>
        <div class="col-md-12 col-lg-6 mb-4">
          <div class="card shadow-sm">
            <div class="card-body">
              <p class="card-text">
              <h3><?= date("d/m/Y H:i", strtotime($p["match_start_date"])) ?> </h3>
              <br>
              <h5> <?= htmlspecialchars($p["league"]) ?> </h5>
              <b class="card-title">
                <?= htmlspecialchars($p["team1"]) ?> <br> <?= htmlspecialchars($p["team2"]) ?>
              </b>


              <form method="POST">
                <input type="hidden" name="match_id" value="<?= $p["id"] ?>">

                <div class="col-md-12 col-lg-6 mb-3">
                  <label class="form-label d-block">Scegli l'esito:</label>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="choice" value="W1" required>
                    <label class="form-check-label">
                      <?= htmlspecialchars($p["team1"]) ?> vince (<?= htmlspecialchars($quote["GW1"]) ?>)
                    </label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="choice" value="X">
                    <label class="form-check-label">
                      Pareggio (<?= htmlspecialchars($quote["GX"]) ?>)
                    </label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="choice" value="W2">
                    <label class="form-check-label">
                      <?= htmlspecialchars($p["team2"]) ?> vince (<?= htmlspecialchars($quote["GW2"]) ?>)
                    </label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="choice" value="G">
                    <label class="form-check-label">
                      Gol (<?= htmlspecialchars($quote["GG"]) ?>)
                    </label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="choice" value="NG">
                    <label class="form-check-label">
                      No gol (<?= htmlspecialchars($quote["GNG"]) ?>)
                    </label>
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label">Importo (€):</label>
                  <input type="number" class="form-control" name="amount" min="1" max="3" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">Scommetti</button>
              </form>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="errorModalLabel">Errore</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <?= !empty($errorMessage) ? htmlspecialchars($errorMessage) : '' ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      <?php if (!empty($errorMessage)): ?>
        var errorModalEl = document.getElementById('errorModal');
        if (errorModalEl) {
          var myModal = new bootstrap.Modal(errorModalEl);
          myModal.show();
        }
      <?php endif; ?>
    });
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
