<?php
$currPage = 'system_worker queue';
include BASE_PATH.'app/controller/PageController.php';

$key = $helper->protect($_GET['key']);

if($key == env('CRONE_KEY')){

    $SQL = $db->prepare("SELECT * FROM `queue` WHERE `retries` = '0'");
    $SQL->execute();
    if ($SQL->rowCount() != 0) {
        while ($row = $SQL->fetch(PDO::FETCH_ASSOC)) {
            $error = null;

            $payload = json_decode($row['payload']);


            if($payload->action == 'PLESK_ORDER'){
                include BASE_PATH.'app/crone/WORKER/plesk_order.php';
                $worker->success($row['id']);
                die('worker done webspace '.$row['id']);
            }

            if($payload->action == 'ROOTSERVER_ORDER'){
                include BASE_PATH.'app/crone/WORKER/rootserver_order.php';
                $worker->success($row['id']);
                die('worker done kvm '.$row['id']);
            }

        }
    }

    die('nothing todo');

} else {
    include BASE_PATH.'resources/sites/404.php';
}
