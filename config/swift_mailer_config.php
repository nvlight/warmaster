<?php

return [

    'mailer' => [
        'class' => 'yii\swiftmailer\Mailer',
        'useFileTransport' => false,
        'transport' => [
            'class' => 'Swift_SmtpTransport',
            'host' =>     $params['sw_host'],
            'username' => $params['sw_frommail'],
            'password' => $params['sw_pass'],
            'port' =>     $params['sw_port'],
            'encryption' => $params['sw_enc'],
        ],
    ],
];