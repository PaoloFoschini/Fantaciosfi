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

    public function addBet(int $match_id, int $user_id, string $choice, float $amount, float $mul)
    {
        $stmt = $this->pdo->prepare("
                INSERT INTO bets (match_id, user_id, choice, amount, payout)
                VALUES (?, ?, ?, ?, ?)
            ");
        $payout = round($amount * $mul, 2);
        return $stmt->execute([$match_id, $user_id, $choice, $amount, $payout]);
    }

    public function getUserBets(int $user_id)
    {
        $sql = "
        SELECT b.*, m.*, t1.name AS team1, t2.name AS team2
        FROM bets b
        JOIN matches m ON b.match_id = m.id
        JOIN teams t1 ON m.team1 = t1.id
        JOIN teams t2 ON m.team2 = t2.id
        WHERE b.user_id = ?
        ORDER BY m.match_start_date DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBetQuotes(int $match_id)
    {
        $stmt = $this->pdo->prepare("SELECT GW1, GW2, GX, GG, GNG FROM matches WHERE id = ?");
        $stmt->execute([$match_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getByMatch(int $matchId): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM bets WHERE match_id=?");
        $stmt->execute([$matchId]);
        return $stmt->fetchAll();
    }

    public function payBet(int $matchId, int $userId, int $payout): void {
        $sql = "UPDATE bets SET paid=1, payout=? WHERE match_id=? AND user_id=?";
        $this->pdo->prepare($sql)->execute([$payout, $matchId, $userId]);
    }
}
