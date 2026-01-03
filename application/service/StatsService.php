<?php
namespace app\service;

use app\model\TestResult;
use app\model\TreeholePost;
use app\common\Database;

class StatsService
{
    private TestResult $tests;
    private TreeholePost $treehole;

    public function __construct()
    {
        $this->tests = new TestResult();
        $this->treehole = new TreeholePost();
    }

    public function overview(): array
    {
        $pdo = Database::connection();
        $tests = $this->tests->all();
        $posts = $this->treehole->all();

        $totalTests = (int)$pdo->query('SELECT COUNT(*) FROM test_results')->fetchColumn();
        $avgScore = (float)$pdo->query('SELECT IFNULL(AVG(score),0) FROM test_results')->fetchColumn();
        $treeholeCount = (int)$pdo->query('SELECT COUNT(*) FROM treehole_posts')->fetchColumn();
        $dailyTests = $this->groupByDate('test_results', 'submitted_at');
        $dailyPosts = $this->groupByDate('treehole_posts', 'created_at');
        $topTags = $this->topTags($posts);
        $scoreDistribution = $this->scoreHistogram($tests);

        return [
            'total_tests' => $totalTests,
            'average_score' => round($avgScore, 2),
            'treehole_posts' => $treeholeCount,
            'daily_tests' => $dailyTests,
            'daily_posts' => $dailyPosts,
            'top_tags' => $topTags,
            'score_distribution' => $scoreDistribution,
        ];
    }

    private function groupByDate(string $table, string $column): array
    {
        $pdo = Database::connection();
        $grouped = [];
        $stmt = $pdo->query("SELECT DATE($column) as d, COUNT(*) as c FROM {$table} GROUP BY DATE($column) ORDER BY d ASC");
        foreach ($stmt->fetchAll() as $row) {
            $grouped[$row['d']] = (int)$row['c'];
        }
        return $grouped;
    }

    private function topTags(array $posts): array
    {
        $counts = [];
        foreach ($posts as $post) {
            foreach ($post['tags'] ?? [] as $tag) {
                $counts[$tag] = ($counts[$tag] ?? 0) + 1;
            }
        }
        arsort($counts);
        return array_slice($counts, 0, 5, true);
    }

    private function scoreHistogram(array $tests): array
    {
        $buckets = [
            '0-20' => 0,
            '21-40' => 0,
            '41-60' => 0,
            '61-80' => 0,
            '81-100' => 0,
        ];
        foreach ($tests as $test) {
            $score = (int)($test['score'] ?? 0);
            if ($score <= 20) {
                $buckets['0-20']++;
            } elseif ($score <= 40) {
                $buckets['21-40']++;
            } elseif ($score <= 60) {
                $buckets['41-60']++;
            } elseif ($score <= 80) {
                $buckets['61-80']++;
            } else {
                $buckets['81-100']++;
            }
        }
        return $buckets;
    }
}
