<?php
namespace app\model;

use function app\common\read_json;
use function app\common\write_json;

class TestResult
{
    private string $storage = __DIR__ . '/../../storage/data/test_results.json';

    public function all(): array
    {
        return read_json($this->storage, []);
    }

    public function add(array $result): void
    {
        $results = $this->all();
        $results[] = $result;
        write_json($this->storage, $results);
    }
}
