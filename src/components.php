<?php
require_once 'DAO/db.php';
require_once 'DAO/UserDAO.php';
function createNavbar($balance, string $target1, string $target2, string $target3)
{
  global $pdo;
  $userDAO = new UserDAO($pdo);
  $user = $userDAO->getUser($_SESSION['user']['email']);
?>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
	<img src="<?= $_SESSION['user']['picture'] ?>" style="max-width: 25px;">
      <a class="navbar-brand" href="home.php"><?= htmlspecialchars($_SESSION['user']['name']. ' ' . $user['teamname']) ?></a>
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
