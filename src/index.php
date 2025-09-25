<?php
session_start();
$pdo = new PDO("mysql:host=localhost;dbname=fantaciosfi;charset=utf8", "root", "");

$user_id = 1;

// carico le partite
$sql = "SELECT p.*, s1.nome AS squadra1, s2.nome AS squadra2
        FROM partite p
        JOIN squadre s1 ON p.squadra1 = s1.id
        JOIN squadre s2 ON p.squadra2 = s2.id";
$partite = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

// saldo utente
$saldo = $pdo->query("SELECT saldo FROM utenti WHERE id=$user_id")->fetchColumn();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $partita_id = $_POST["partita_id"];
    $scelta = $_POST["scelta"];
    $importo = (int)$_POST["importo"];

    if ($saldo < $importo) {
        die("Saldo insufficiente!");
    }

    $stmt = $pdo->prepare("INSERT INTO giocate (partite_id, user_id, scelta, importo) VALUES (?, ?, ?, ?)");
    $stmt->execute([$partita_id, $user_id, $scelta, $importo]);

    $stmt = $pdo->prepare("UPDATE utenti SET saldo = saldo - ? WHERE id = ?");
    $stmt->execute([$importo, $user_id]);

    header("Location: storico.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Homepage - Scommesse</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container">
    <a class="navbar-brand" href="#">Fantaciosfi</a>
    <div class="d-flex">
      <span class="navbar-text text-white me-3">Saldo: €<?= htmlspecialchars($saldo) ?></span>
      <a href="storico.php" class="btn btn-outline-light btn-sm">Storico</a>
    </div>
  </div>
</nav>

<div class="container">
  <h1 class="mb-4">Partite disponibili</h1>

  <div class="row">
    <?php foreach ($partite as $p): ?>
      <div class="col-md-6 col-lg-4 mb-4">
        <div class="card shadow-sm">
          <div class="card-body">
            <h5 class="card-title">
              <?= htmlspecialchars($p["squadra1"]) ?> vs <?= htmlspecialchars($p["squadra2"]) ?>
            </h5>
            <p class="card-text">
              <b>Data:</b> <?= htmlspecialchars($p["data_partita"]) ?>
            </p>

            <form method="POST">
              <input type="hidden" name="partita_id" value="<?= $p["id"] ?>">

              <div class="mb-3">
                <label class="form-label d-block">Scegli l'esito:</label>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="scelta" value="1" required>
                  <label class="form-check-label"><?= htmlspecialchars($p["squadra1"]) ?> vince</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="scelta" value="X">
                  <label class="form-check-label">Pareggio</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="scelta" value="2">
                  <label class="form-check-label"><?= htmlspecialchars($p["squadra2"]) ?> vince</label>
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label">Importo (€):</label>
                <input type="number" class="form-control" name="importo" min="1" max="<?= $saldo ?>" required>
              </div>

              <button type="submit" class="btn btn-primary w-100">Scommetti</button>
            </form>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
