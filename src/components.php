<?php
require_once 'DAO/db.php';
require_once 'DAO/UserDAO.php';
function createNavbar($balance, $target1, $target2, $target3)
{
  global $pdo;
  $userDAO = new UserDAO($pdo);
  $user = $userDAO->getUser(1);
?>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
      <a class="navbar-brand" href="index.php"><?= htmlspecialchars($user['name']. ' ' . $user['surname']) ?></a>
      <div class="d-flex">
        <span class="navbar-text text-white me-3">
          Saldo: <span class="badge bg-success"><?= htmlspecialchars($balance) ?></span>
        </span>
        <a href="<?php echo $target1 ?>.php" class="btn btn-outline-light btn-sm"><?php echo "Scommesse giocabili" ?></a>
        <a href="<?php echo $target2 ?>.php" class="btn btn-outline-light btn-sm"><?php echo "Scommesse aperte" ?></a>
        <a href="<?php echo $target3 ?>.php" class="btn btn-outline-light btn-sm"><?php echo "Storico" ?></a>
      </div>
    </div>
  </nav>
<?php }; ?>