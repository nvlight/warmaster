<?php

session_start();

if (!array_key_exists('app_start', $_SESSION)){
    $rs = ['success' => 0, 'message' => 'something gone wrong!'];
    die(json_encode($rs));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
    $rs = ['success' => 0, 'message' => 'something gone wrong again!'];
    die(json_encode($rs));
}

//
$rs = ['success' => 1, 'message' => 'we recive the current stage!', 'stage' => $_SESSION['user']['stage']];
die(json_encode($rs));

?>