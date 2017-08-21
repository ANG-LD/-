<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/6/28
 * Time: 11:20
 */
use Workerman\Worker;
require_once __DIR__ . '/Workerman-master/Autoloader.php';

$global_uid = 0;
// ���ͻ���������ʱ����uid�����������ӣ���֪ͨ���пͻ���
function handle_connection($connection)
{
    global $text_worker, $global_uid;
    // Ϊ������ӷ���һ��uid
    $connection->uid = ++$global_uid;
}

// ���ͻ��˷�����Ϣ����ʱ��ת����������
function handle_message($connection, $data)
{
    global $text_worker;
    foreach($text_worker->connections as $conn)
    {
        $conn->send("user[{$connection->uid}] said: $data");
    }
}

// ���ͻ��˶Ͽ�ʱ���㲥�����пͻ���
function handle_close($connection)
{
    global $text_worker;
    foreach($text_worker->connections as $conn)
    {
        $conn->send("user[{$connection->uid}] logout");
    }
}

// ����һ��Worker����2345�˿ڣ�ʹ��httpЭ��ͨѶ
$http_worker = new Worker("text://0.0.0.0:2347");

// ֻ����1�����̣���������ͻ���֮�䴫������
$text_worker->count = 1;

$text_worker->onConnect = 'handle_connection';
$text_worker->onMessage = 'handle_message';
$text_worker->onClose = 'handle_close';

Worker::runAll();