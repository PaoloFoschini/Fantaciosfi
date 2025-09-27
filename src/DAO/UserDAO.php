<?php
class UserDAO
{
    private readonly PDO $pdo;
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    public function getAll()
    {
        $stmt = $this->pdo->query("SELECT * FROM users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function updateBalance($newBalance, int $userId): true
    {
        $stmt = $this->pdo->prepare("UPDATE users SET balance = ? WHERE id = ?");
        $stmt->execute([$newBalance, $userId]);
        return true;
    }
    public function updateBalanceAndRole(int $id, int $balance, int $role): void
    {
        $stmt = $this->pdo->prepare("UPDATE users SET balance=?, role=? WHERE id=?");
        $stmt->execute([$balance, $role, $id]);
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }
    public function getUser(string $email): array|false
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function incrementBalance(int $id, int $amount): void {
        $stmt = $this->pdo->prepare("UPDATE users SET balance = balance + ? WHERE id=?");
        $stmt->execute([$amount, $id]);
    }
}
