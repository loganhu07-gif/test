<?php
namespace app\controller;

use app\model\TestResult;
use function app\common\json_response;
use function app\common\now;

class Test
{
    private TestResult $tests;

    public function __construct()
    {
        $this->tests = new TestResult();
    }

    public function submit(): void
    {
        $answers = $_POST['answers'] ?? [];
        $score = $this->calculateScore($answers);
        $this->tests->add([
            'answers' => $answers,
            'score' => $score,
            'submitted_at' => now(),
        ]);
        json_response(['message' => '提交成功', 'score' => $score]);
    }

    private function calculateScore(array $answers): int
    {
        if (!$answers) {
            return 0;
        }
        $score = 0;
        foreach ($answers as $value) {
            $score += (int)$value;
        }
        return min(100, $score);
    }
}
