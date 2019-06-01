<?php

session_start();

$stage = intval($_POST['stage']);
$_SESSION['user']['stage'] = $stage;
//
$rs = ['success' => 1,
    'message' => 'go to custom stage',
    'stage' => $_SESSION['user']['stage'],
];
die(json_encode($rs));

?>