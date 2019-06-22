<?php

require '../lib/functions.php';
require '../vendor/autoload.php';

// prepare need form keys and patterns for checking...
$need_form_keys = [
    ['User name','username','^[a-zA-Z_]+([a-zA-Z\d_]+){1,32}$', 'Имя пользователя'],
    ['User password','userpassword','^([a-zA-Z\d@!_-]+){4,33}$', 'Пароль'],
    ['User password','userpassword_re','^([a-zA-Z\d@!_-]+){4,33}$', 'Повтор пароля'],
    ['User mail','mail','^[a-zA-Z_]+@[a-zA-Z\d_]+\.[a-zA-Z\d_]+', 'Емейл'],
    //['Captcha','sup_captcha','^[a-z\d]+$'],
];
$additional_form_keys = [
// empty

];

$subject = "Message from main_site";
$msg_header = 'Запрос - получить консультацию!';

function mySendMailMessage($subject, $msg_header, $need_form_keys, $additional_form_keys)
{
    $myParams = require '../config/params.php'; $params = $myParams;
    $myConfig = require '../config/swift_mailer_config.php';
    $myParams['sw_subject'] = $subject;
    echo Debug::d($myParams);
    echo Debug::d($myConfig);

    try {
        // Create the SMTP Transport
        $transport = (new Swift_SmtpTransport($myConfig['mailer']['transport']['host'],
            $myConfig['mailer']['transport']['port']))
            ->setUsername($myConfig['mailer']['transport']['username'])
            ->setPassword($myConfig['mailer']['transport']['password'])
            ->setEncryption($myParams['sw_enc']);

        // Create the Mailer using your created Transport
        $mailer = new Swift_Mailer($transport);

        // Create a message
        $message = new Swift_Message();

        // Set a "subject"
        $message->setSubject($myParams['sw_subject']);

        // Set the "From address"
        $message->setFrom([$myParams['sw_frommail'] => $myParams['my_name']]);

        // Set the "To address" [Use setTo method for multiple recipients, argument should be array]
        $message->addTo( $myParams['sw_tomail2'],'recipient name');

        // Add "CC" address [Use setCc method for multiple recipients, argument should be array]
        //$message->addCc('recipient@gmail.com', 'recipient name');

        // Add "BCC" address [Use setBcc method for multiple recipients, argument should be array]
        //$message->addBcc('recipient@gmail.com', 'recipient name');

        // Add an "Attachment" (Also, the dynamic data can be attached)
        //$attachment = Swift_Attachment::fromPath('example.xls');
        //$attachment->setFilename('report.xls');
        //$message->attach($attachment);

        // Add inline "Image"
        //$inline_attachment = Swift_Image::fromPath('nature.jpg');
        //$cid = $message->embed($inline_attachment);

        // Set the plain-text "Body"
        //$message->setBody("This is the plain text body of the message.\nThanks,\nAdmin");

        $message->addPart($msg_header, 'text/html');

        foreach($need_form_keys as $k => $v){
            $clear_val = Debug::encode($v[1]);
            $message->addPart($v[0] . ': ' . $clear_val, 'text/html');
        }

        // Send the message
        $result = $mailer->send($message);
        $rs = ['success' => 0, 'message' => 'we send the message!',
            'add_info' => $result,
        ];

        die(json_encode($rs));

    } catch (Exception $e) {
        $rs = ['success' => 0, 'message' => $e->getMessage() ];
        die(json_encode($rs));
    }
}

mySendMailMessage($subject, $msg_header, $need_form_keys, $additional_form_keys);

?>