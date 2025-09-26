<?php 
function createNavbar($balance, $target, $name) {
    ?>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
      <div class="container">
        <a class="navbar-brand" href="index.php">Fantaschedine</a>
        <div class="d-flex">
          <span class="navbar-text text-white me-3">
            Saldo: <span class="badge bg-success">€<?= htmlspecialchars($balance) ?></span>
          </span>
          <a href="<?php echo $target ?>.php" class="btn btn-outline-light btn-sm"><?php echo $name ?></a>
        </div>
      </div>
    </nav>
<?php };?>
