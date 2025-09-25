<?php
session_start();
$pdo = new PDO("mysql:host=localhost;dbname=fantaciosfi;charset=utf8", "root", "");
$user_id = 1;

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

$saldo = $pdo->query("SELECT saldo FROM utenti WHERE id=$user_id")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Storico</title>
    <link rel="stylesheet" href="static/styles.css">
</head>
<body>
<header>
    <h1>Storico scommesse</h1>
</header>

<div class="table-container">
    <h2>Saldo attuale: <?= $saldo ?> crediti</h2>
    <table>
        <thead>
            <tr>
                <th>Partita</th>
                <th>Data</th>
                <th>Scelta</th>
                <th>Importo</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($giocate as $g): ?>
                <tr>
                    <td><?= htmlspecialchars($g["squadra1"]) ?> vs <?= htmlspecialchars($g["squadra2"]) ?></td>
                    <td><?= $g["data_partita"] ?></td>
                    <td><?= htmlspecialchars($g["scelta"]) ?></td>
                    <td><?= $g["importo"] ?> crediti</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
