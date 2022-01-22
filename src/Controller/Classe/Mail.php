<?php

namespace App\Classe;

use Mailjet\Client;
use Mailjet\Resources;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;


class Mail {

    private $mail_api_key = '432ca58e12f88281a1f52d058a597523';
    private $mail_api_key_secret = 'a5acd63593b084cd328cd31d9d1326d4';
    public function send($toEmail, $toName, $subject,$content){

        $mj = new Client($this->mail_api_key, $this->mail_api_key_secret,true,['version' => 'v3.1']);

        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "anaellejeagni@gmail.com",
                        'Name' => "Andy's Crochet"
                    ],
                    'To' => [
                        [
                            'Email' => $toEmail,
                            'Name' => $toName,
                        ]
                    ],
                    'TemplateID' => 3472701,
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    "Variables" => [
                        "content" => $content,

						]
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success();
    }
}