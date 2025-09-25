<?php
session_start();
$pdo = new PDO("mysql:host=localhost;dbname=fantaciosfi;charset=utf8", "root", "");

$user_id = 1;

$sql = "SELECT p.*, s1.nome AS squadra1, s2.nome AS squadra2
        FROM partite p
        JOIN squadre s1 ON p.squadra1 = s1.id
        JOIN squadre s2 ON p.squadra2 = s2.id";
$partite = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $partita_id = $_POST["partita_id"];
    $scelta = $_POST["scelta"];
    $importo = (int)$_POST["importo"];

    $saldo = $pdo->query("SELECT saldo FROM utenti WHERE id=$user_id")->fetchColumn();
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
    <link rel="stylesheet" href="static/styles.css">
</head>
<body>
<header>
    <h1>Homepage - Scommesse</h1>
</header>

<div class="matches">
<?php foreach ($partite as $p): ?>
    <div class="match-card">
        <h2><?= htmlspecialchars($p["squadra1"]) ?> vs <?= htmlspecialchars($p["squadra2"]) ?></h2>
        <p><b>Data:</b> <?= $p["data_partita"] ?></p>

        <form method="POST">
            <input type="hidden" name="partita_id" value="<?= $p["id"] ?>">

            <div class="odds">
                <label><input ty
