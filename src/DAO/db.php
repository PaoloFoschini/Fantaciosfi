<?php
    $pdo = match(PROD) {
        true => new PDO('mysql:unix_socket=/var/run/mysqld/mysqld.sock;dbname=fantaciosfi;charset=utf8mb4;user=www-data'),
        false => new PDO('mysql:host=localhost;dbname=fantaciosfi;charset=utf8mb4', 'root', ''),
    };
?>
