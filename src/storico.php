<?php
session_start();
$pdo = new PDO("mysql:host=localhost;dbname=fantaciosfi;charset=utf8", "root", "");
$user_id = 1;

// query giocate utente
$sql = "SELECT g.*, p.data_partita, s1.nome AS squadra1, s2.nome AS squadra2
        FROM giocate g
        JOIN partite p ON g.partite_id = p.id
        JOIN squadre s1 ON p.squadra1 = s1.id
        JOIN squadre s2 ON p.squadra2 = s2.id
        WHERE g.user_id = ?
        ORDER BY p.data_partita DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$giocate = $stmt->fetchAll(PDO::FETCH_ASSOC);

// saldo utente
$saldo = $pdo->query("SELECT saldo FROM utenti WHERE id=$user_id")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Storico scommesse</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container">
    <a class="navbar-brand" href="index.php">Scommesse</a>
    <div class="d-flex">
      <span class="navbar-text text-white me-3">
        Saldo: <span class="badge bg-success">€<?= htmlspecialchars($saldo) ?></span>
      </span>
      <a href="index.php" class="btn btn-outline-light btn-sm">Torna alle partite</a>
    </div>
  </div>
</nav>

<div class="container">
  <h1 class="mb-4">Storico delle tue scommesse</h1>

  <?php if (count($giocate) > 0): ?>
    <div class="table-responsive">
      <table class="table table-striped table-hover align-middle shadow-sm">
        <thead class="table-dark">
          <tr>
            <th scope="col">Partita</th>
            <th scope="col">Data</th>
            <th scope="col">Scelta</th>
            <th scope="col">Importo</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($giocate as $g): ?>
            <tr>
              <td><?= htmlspecialchars($g["squadra1"]) ?> vs <?= htmlspecialchars($g["squadra2"]) ?></td>
              <td><?= htmlspecialchars($g["data_partita"]) ?></td>
              <td>
                <?php if ($g["scelta"] === "1"): ?>
                  <span class="badge bg-primary"><?= htmlspecialchars($g["squadra1"]) ?> vince</span>
                <?php elseif ($g["scelta"] === "2"): ?>
                  <span class="badge bg-danger"><?= htmlspecialchars($g["squadra2"]) ?> vince</span>
                <?php else: ?>
                  <span class="badge bg-warning text-dark">Pareggio</span>
                <?php endif; ?>
              </td>
              <td><span class="fw-bold"><?= htmlspecialchars($g["importo"]) ?></span> crediti</td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <div class="alert alert-info">Non hai ancora effettuato scommesse.</div>
  <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
