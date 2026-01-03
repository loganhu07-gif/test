<?php
// Simple front controller emulating a ThinkPHP5-style entry point for a demo project.
require_once __DIR__ . '/../application/common/bootstrap.php';

use app\controller\Auth;
use app\controller\Test;
use app\controller\Treehole;
use app\controller\Admin;
use app\controller\Stats;
use app\controller\Backup;

$path = $_GET['r'] ?? '';
switch ($path) {
    case 'auth/login':
        (new Auth())->login();
        break;
    case 'auth/captcha':
        (new Auth())->captcha();
        break;
    case 'test/submit':
        (new Test())->submit();
        break;
    case 'treehole/post':
        (new Treehole())->post();
        break;
    case 'treehole/list':
        (new Treehole())->index();
        break;
    case 'admin/dashboard':
        (new Admin())->dashboard();
        break;
    case 'admin/upload':
        (new Admin())->upload();
        break;
    case 'stats/overview':
        (new Stats())->overview();
        break;
    case 'backup/create':
        (new Backup())->create();
        break;
    case 'backup/restore':
        (new Backup())->restore();
        break;
    case 'backup/list':
        (new Backup())->list();
        break;
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Route not found', 'routes' => [
            'auth/login', 'auth/captcha', 'test/submit', 'treehole/post', 'treehole/list',
            'admin/dashboard', 'admin/upload', 'stats/overview', 'backup/create', 'backup/restore', 'backup/list'
        ]], JSON_UNESCAPED_UNICODE);
}
