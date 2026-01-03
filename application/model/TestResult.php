<?php
namespace app\model;

use app\common\Database;

class TestResult
{
    public function all(): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->query('SELECT id, answers, score, submitted_at FROM test_results ORDER BY id DESC');
        return $stmt->fetchAll();
    }

    public function add(array $result): void
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('INSERT INTO test_results (answers, score, submitted_at) VALUES (:answers, :score, :submitted_at)');
        $stmt->execute([
            'answers' => json_encode($result['answers'], JSON_UNESCAPED_UNICODE),
            'score' => $result['score'],
            'submitted_at' => $result['submitted_at'],
        ]);
    }
}
