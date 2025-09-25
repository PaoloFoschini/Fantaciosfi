<?php 
class TeamDAO{
    private readonly PDO $pdo;
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    public function getAll(){
        $stmt = $this->pdo->query("SELECT * FROM teams");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

?> 