<?php
class MatchDAO
{
    private PDO $pdo;
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAll(): array
    {
        $sql = "SELECT m.*, t1.name as team1name, t2.name as team2name
                FROM matches m
                JOIN teams t1 ON m.team1=t1.id
                JOIN teams t2 ON m.team2=t2.id
                ORDER BY m.id DESC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM matches WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function insert(
        int $team1,
        int $team2,
        string $start,
        string $league,
        float $gw1,
        float $gx,
        float $gw2,
        float $gg,
        float $gng
    ): void {
        $sql = "INSERT INTO matches(team1, team2, match_start_date, league, GW1, GX, GW2, GG, GNG)
                VALUES (?,?,?,?,?,?,?,?,?)";
        $this->pdo->prepare($sql)->execute([$team1, $team2, $start, $league, $gw1, $gx, $gw2, $gg, $gng]);
    }

    public function setResult(int $id, string $result): void
    {
        $sql = "UPDATE matches SET result=?, isFinished=true WHERE id=?";
        $this->pdo->prepare($sql)->execute([$result, $id]);
    }
    public function getQuotaByChoice(int $match_id, string $choice): ?float
    {
        $stmt = $this->pdo->prepare("
        SELECT 
            CASE ?
                WHEN 'W1' THEN GW1
                WHEN 'X'  THEN GX
                WHEN 'W2' THEN GW2
                WHEN 'G'  THEN GG
                WHEN 'NG' THEN GNG
            END AS quota
        FROM matches
        WHERE id = ?
    ");
        $stmt->execute([$choice, $match_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (float)$row['quota'] : null;
    }
}
