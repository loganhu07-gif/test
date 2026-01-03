<?php
namespace app\controller;

use app\service\StatsService;
use function app\common\json_response;

class Stats
{
    private StatsService $stats;

    public function __construct()
    {
        $this->stats = new StatsService();
    }

    public function overview(): void
    {
        json_response($this->stats->overview());
    }
}
