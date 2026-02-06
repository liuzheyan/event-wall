<?php
    // 设置响应头，允许跨域
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    header('Content-Type: application/json');

    // 处理预检请求
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        exit(0);
    }

    // 只接收 POST 请求
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['code' => 405, 'msg' => 'Method Not Allowed']);
        exit;
    }

    // 获取原始 POST 数据
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    // 验证数据
    if (!isset($data['text']) || empty($data['text'])) {
        http_response_code(400);
        echo json_encode(['code' => 400, 'msg' => 'Missing text parameter']);
        exit;
    }

    $text = $data['text'];
    $time = isset($data['time']) ? date('Y-m-d H:i:s', $data['time'] / 1000) : date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'];

    // 格式化日志内容
    $logLine = sprintf("[%s] [IP:%s] %s" . PHP_EOL, $time, $ip, $text);

    // 定义日志文件路径 (上一级目录的 danmu_data.txt)
    $logFile = '../data/danmu_data.txt';

    // 写入文件 (追加模式)
    if (file_put_contents($logFile, $logLine, FILE_APPEND | LOCK_EX) !== false) {
        echo json_encode(['code' => 200, 'msg' => 'Success']);
    } else {
        http_response_code(500);
        echo json_encode(['code' => 500, 'msg' => 'Failed to write file']);
    }
?>