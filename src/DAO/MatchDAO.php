<?php 
class MatchDAO{
    private readonly PDO $pdo;
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    public function getAll(){
        $stmt = $this->pdo->query("SELECT m.*, t1.name AS team1, t2.name AS team2
        FROM matches m
        JOIN teams t1 ON m.team1 = t1.id
        JOIN teams t2 ON m.team2 = t2.id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>