<?php
class TeamDAO {
    private PDO $pdo;
    public function __construct(PDO $pdo){ $this->pdo = $pdo; }

    public function getAll(): array {
        return $this->pdo->query("SELECT * FROM teams ORDER BY id")->fetchAll();
    }

    public function getById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM teams WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function insert(string $name): void {
        $stmt = $this->pdo->prepare("INSERT INTO teams(name) VALUES(?)");
        $stmt->execute([$name]);
    }

    public function update(int $id, string $name): void {
        $stmt = $this->pdo->prepare("UPDATE teams SET name=? WHERE id=?");
        $stmt->execute([$name, $id]);
    }

    public function delete(int $id): void {
        $stmt = $this->pdo->prepare("DELETE FROM teams WHERE id=?");
        $stmt->execute([$id]);
    }
}

?> 