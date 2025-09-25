<?php
class BetDAO
{
    private readonly PDO $pdo;
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAll()
    {
        $stmt = $this->pdo->query("SELECT * FROM bets");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addBet(int $match_id, int $user_id, string $choice, float $amount)
    {
        $stmt = $this->pdo->prepare("
                INSERT INTO bets (match_id, user_id, choice, amount)
                VALUES (?, ?, ?, ?)
            ");
        return $stmt->execute([$match_id, $user_id, $choice, $amount]);
    }

    public function getUserBets(int $user_id)
    {
        $sql = "
        SELECT b.*, m.match_date, t1.name AS team1, t2.name AS team2
        FROM bets b
        JOIN matches m ON b.match_id = m.id
        JOIN teams t1 ON m.team1 = t1.id
        JOIN teams t2 ON m.team2 = t2.id
        WHERE b.user_id = ?
        ORDER BY m.match_date DESC
    ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
