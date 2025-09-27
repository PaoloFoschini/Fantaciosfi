<?php
require_once __DIR__ . '/config.php';
require __DIR__ . '/components.php';
require_once __DIR__ . '/DAO/db.php';
require_once __DIR__ . '/DAO/MatchDAO.php';
require_once __DIR__ . '/DAO/TeamDAO.php';
require_once __DIR__ . '/DAO/BetDAO.php';
require_once __DIR__ . '/DAO/UserDAO.php';

if (PROD && !isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$matchDAO = new MatchDAO($pdo);
$userDAO  = new UserDAO($pdo);
$betDAO   = new BetDAO($pdo);
$teamDAO  = new TeamDAO($pdo);

// ==== FUNZIONI UTILI ====
function h($s)
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}
function quotaByChoice($m, $c)
{
    return match ($c) {
        "W1" => $m['GW1'],
        "X"  => $m['GX'],
        "W2" => $m['GW2'],
        "G"  => $m['GG'],
        "NG" => $m['GNG'],
    };
}

// ==== AZIONI FORM ====
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // TEAM
    if (isset($_POST["add_team"])) {
        $teamDAO->insert($_POST["name"]);
    }
    if (isset($_POST["edit_team"])) {
        $teamDAO->update((int)$_POST["id"], $_POST["name"]);
    }
    if (isset($_POST["del_team"])) {
        $teamDAO->delete((int)$_POST["id"]);
    }

    // USER
    if (isset($_POST["edit_user"])) {
        $userDAO->updateBalanceAndRole((int)$_POST["id"], (int)$_POST["balance"], (int)$_POST["role"]);
    }

    // MATCH
    if (isset($_POST["add_match"])) {
        $matchDAO->insert(
            (int)$_POST["team1"],
            (int)$_POST["team2"],
            $_POST["start"],
            $_POST["league"],
            (float)$_POST["GW1"],
            (float)$_POST["GX"],
            (float)$_POST["GW2"],
            (float)$_POST["GG"],
            (float)$_POST["GNG"]
        );
    }
    if (isset($_POST["set_result"])) {
        $matchId = (int)$_POST["id"];
        $score   = trim($_POST["score"]); // es: "2-1"

        // parsing risultato
        if (!preg_match('/^(\d+)-(\d+)$/', $score, $m)) {
            die("Formato risultato non valido (usa es: 2-1)");
        }
        $g1 = (int)$m[1];
        $g2 = (int)$m[2];

        // determina esiti
        $esiti = [];

        if ($g1 > $g2) $esiti[] = "W1";
        elseif ($g2 > $g1) $esiti[] = "W2";
        else $esiti[] = "X";

        if ($g1 > 0 && $g2 > 0) $esiti[] = "G";
        else $esiti[] = "NG";

        // salva risultato e close match
        $matchDAO->setResult($matchId, $score);

        // paga bet
        $match = $matchDAO->getById($matchId);
        $bets  = $betDAO->getByMatch($matchId);

        foreach ($bets as $b) {
            if ($b['paid']) continue;
            $payout = 0;
            if (in_array($b['choice'], $esiti, true)) {
                $quota  = quotaByChoice($match, $b['choice']);
                $payout = floor($b['amount'] * $quota);
                $userDAO->incrementBalance((int)$b['user_id'], $payout);
            }
            $betDAO->payBet($matchId, (int)$b['user_id'], $payout);
        }
    }


    header("Location: admin.php");
    exit;
}

// ==== DATI PER TABELLE ====
$teams   = $teamDAO->getAll();
$users   = $userDAO->getAll();
$matches = $matchDAO->getAll();
$bets    = $betDAO->getAll();

?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse
        }

        th,
        td {
            padding: 6px;
            border: 1px solid #ddd
        }
    </style>
</head>

<body>
    <h1>Admin Panel</h1>

    <h2>Teams</h2>
    <form method="post">
        <input name="name" placeholder="Team name" required>
        <button name="add_team">Add</button>
    </form>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($teams as $t): ?>
            <tr>
                <form method="post">
                    <td><?= h($t['id']) ?><input type="hidden" name="id" value="<?= $t['id'] ?>"></td>
                    <td><input name="name" value="<?= h($t['name']) ?>"></td>
                    <td>
                        <button name="edit_team">Save</button>
                        <button name="del_team" onclick="return confirm('Delete team?')">Delete</button>
                    </td>
                </form>
            </tr>
        <?php endforeach; ?>
    </table>

    <h2>Matches</h2>
    <form method="post">
        <select name="team1"><?php foreach ($teams as $t) echo "<option value='{$t['id']}'>" . h($t['name']) . "</option>"; ?></select>
        <select name="team2"><?php foreach ($teams as $t) echo "<option value='{$t['id']}'>" . h($t['name']) . "</option>"; ?></select>
        <input type="datetime-local" name="start" required>
        <input name="league" placeholder="League" required>
        GW1<input name="GW1" size="3"> GX<input name="GX" size="3"> GW2<input name="GW2" size="3">
        GG<input name="GG" size="3"> GNG<input name="GNG" size="3">
        <button name="add_match">Add match</button>
    </form>
    <table>
        <tr>
            <th>ID</th>
            <th>Teams</th>
            <th>League</th>
            <th>Start</th>
            <th>Result</th>
            <th>Action</th>
        </tr>
        <?php foreach ($matches as $m): ?>
            <?php if(!$m['isFinished']): ?>
            <tr>
                <form method="post">
                    <td><?= $m['id'] ?><input type="hidden" name="id" value="<?= $m['id'] ?>"></td>
                    <td><?= h($m['team1name']) . " vs " . h($m['team2name']) ?></td>
                    <td><?= h($m['league']) ?></td>
                    <td><?= h($m['match_start_date']) ?></td>
                    <td><?= h($m['result'] ?? '-') ?></td>
                    <td>
                        <input type="text" name="score" placeholder="es: 2-1" required>
                        <button name="set_result">Close & Pay</button>
                    </td>
                </form>
            </tr>
        <?php endif; endforeach; ?>
    </table>

    <h2>Users</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Email</th>
            <th>Team</th>
            <th>Role</th>
            <th>Balance</th>
            <th>Action</th>
        </tr>
        <?php foreach ($users as $u): ?>
            <tr>
                <form method="post">
                    <td><?= $u['id'] ?><input type="hidden" name="id" value="<?= $u['id'] ?>"></td>
                    <td><?= h($u['email']) ?></td>
                    <td><?= h($u['teamname']) ?></td>
                    <td><input name="role" value="<?= $u['role'] ?>" size="3"></td>
                    <td><input name="balance" value="<?= $u['balance'] ?>" size="6"></td>
                    <td><button name="edit_user">Save</button></td>
                </form>
            </tr>
        <?php endforeach; ?>
    </table>

</body>

</html>