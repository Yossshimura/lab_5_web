<?php
class QuizParticipant {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function add($name, $age, $topic, $prize, $difficulty) {
        $stmt = $this->pdo->prepare(
            "INSERT INTO quiz_participants (name, age, topic, prize, difficulty) VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([$name, $age, $topic, $prize, $difficulty]);
    }

    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM quiz_participants ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM quiz_participants WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function update($id, $name, $age, $topic, $prize, $difficulty) {
        $stmt = $this->pdo->prepare(
            "UPDATE quiz_participants SET name = ?, age = ?, topic = ?, prize = ?, difficulty = ? WHERE id = ?"
        );
        $stmt->execute([$name, $age, $topic, $prize, $difficulty, $id]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM quiz_participants WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function getCount() {
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM quiz_participants");
        $result = $stmt->fetch();
        return $result['total'];
    }

    public function getCountWithFilter($minAge = null) {
        if ($minAge !== null) {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) as total FROM quiz_participants WHERE age >= ?");
            $stmt->execute([$minAge]);
        } else {
            $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM quiz_participants");
        }
        $result = $stmt->fetch();
        return $result['total'];
    }

    public function getByAge($minAge) {
        $stmt = $this->pdo->prepare("SELECT * FROM quiz_participants WHERE age >= ? ORDER BY created_at DESC");
        $stmt->execute([$minAge]);
        return $stmt->fetchAll();
    }
}
?>